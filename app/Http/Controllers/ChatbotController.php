<?php

namespace App\Http\Controllers;

use App\Models\Sach;
use App\Models\TheLoai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatbotController extends Controller
{
    /**
     * Xử lý tin nhắn chatbot — Gọi Gemma 3 27B IT qua Google AI Studio.
     */
    public function chat(Request $request)
    {
        $request->validate([
            'message' => ['required', 'string', 'max:500'],
        ]);

        $userMessage = $request->input('message');
        $apiKey = config('services.gemini.api_key');

        if (empty($apiKey) || $apiKey === 'your-api-key-here') {
            return response()->json([
                'reply' => 'Chatbot chưa được cấu hình. Vui lòng thêm GEMINI_API_KEY vào file .env.',
            ], 500);
        }

        // ── Tìm sách liên quan trong DB để làm context ──────────────
        $bookContext = $this->getBookContext($userMessage);

        // ── System Prompt ───────────────────────────────────────────
        $systemPrompt = <<<PROMPT
Bạn là trợ lý AI của nhà sách trực tuyến "Modtra Books". Nhiệm vụ của bạn:
- Tư vấn, gợi ý sách phù hợp với nhu cầu khách hàng
- Trả lời câu hỏi về sách, thể loại, tác giả
- Hỗ trợ tìm kiếm sách theo mô tả ngôn ngữ tự nhiên
- Trả lời thân thiện, ngắn gọn, bằng tiếng Việt
- Khi gợi ý sách, hãy trình bày dạng danh sách ngắn gọn với tên sách, tác giả và giá
- Nếu không tìm thấy sách phù hợp, hãy gợi ý khách thử từ khóa khác hoặc liên hệ nhà sách
- Không trả lời các câu hỏi không liên quan đến sách hoặc nhà sách

Dữ liệu sách hiện có trong cửa hàng:
{$bookContext}
PROMPT;

        // ── Gọi Gemini Flash Lates API ─────────────────────────────────
        try {
            $response = Http::withoutVerifying()->timeout(30)->post(
                "https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-latest:generateContent?key={$apiKey}",
                [
                    'system_instruction' => [
                        'parts' => [['text' => $systemPrompt]],
                    ],
                    'contents' => [
                        [
                            'role'  => 'user',
                            'parts' => [['text' => $userMessage]],
                        ],
                    ],
                    'generationConfig' => [
                        'temperature'     => 0.7,
                        'maxOutputTokens' => 1024,
                        'topP'            => 0.9,
                    ],
                ]
            );

            if ($response->successful()) {
                $data  = $response->json();
                $reply = $data['candidates'][0]['content']['parts'][0]['text'] ?? 'Xin lỗi, tôi không thể trả lời lúc này.';

                return response()->json(['reply' => $reply]);
            }

            Log::error('Gemini API error', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);

            return response()->json([
                'reply' => 'Xin lỗi, có lỗi xảy ra khi kết nối AI. Vui lòng thử lại sau.',
            ], 500);

        } catch (\Exception $e) {
            Log::error('Chatbot exception: ' . $e->getMessage());

            return response()->json([
                'reply' => 'Xin lỗi, dịch vụ AI tạm thời không khả dụng. Vui lòng thử lại sau.',
            ], 500);
        }
    }

    /**
     * Tìm sách liên quan từ DB dựa trên tin nhắn của user.
     */
    private function getBookContext(string $message): string
    {
        // Tách keyword từ tin nhắn
        $keywords = array_filter(explode(' ', $message), fn($w) => mb_strlen($w) >= 2);

        $query = Sach::with(['tacGia', 'theLoai']);

        if (!empty($keywords)) {
            $query->where(function ($q) use ($keywords) {
                foreach ($keywords as $keyword) {
                    $q->orWhere('tieu_de', 'LIKE', "%{$keyword}%")
                      ->orWhere('mo_ta', 'LIKE', "%{$keyword}%");
                }
            });
        }

        $books = $query->take(10)->get();

        // Nếu không tìm thấy sách liên quan, lấy sách mới nhất
        if ($books->isEmpty()) {
            $books = Sach::with(['tacGia', 'theLoai'])
                ->orderByDesc('created_at')
                ->take(10)
                ->get();
        }

        if ($books->isEmpty()) {
            return 'Chưa có sách nào trong cửa hàng.';
        }

        // Danh sách thể loại
        $categories = TheLoai::pluck('ten_the_loai')->implode(', ');

        $context = "Các thể loại sách: {$categories}\n\nDanh sách sách:\n";

        foreach ($books as $book) {
            $author   = $book->tacGia->ten_tac_gia ?? 'Không rõ';
            $category = $book->theLoai->ten_the_loai ?? 'Chưa phân loại';
            $price    = number_format($book->gia_ban, 0, ',', '.') . 'đ';
            $stock    = $book->conHang() ? 'Còn hàng' : 'Hết hàng';

            $context .= "- \"{$book->tieu_de}\" — Tác giả: {$author} | Thể loại: {$category} | Giá: {$price} | {$stock}\n";
        }

        return $context;
    }
}