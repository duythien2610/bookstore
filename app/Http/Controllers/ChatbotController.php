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
     * Lấy lịch sử chat
     */
    public function generateContent()
    {
        $url= "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash-lite:generateContent?key=".env('GEMINI_API_KEY');
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])->post($url, [
            'contents' => [
                [
                    'parts' => [
                        ['text' => 'Hello, how are you?'],
                    ],
                ],
            ],
        ]);
        return $response->json();   
    }
    public function fetchMessage(Request $request)
    {
        $userId = auth()->id();
        $guestToken = $request->cookie('guest_chat_token');

        if (!$userId && !$guestToken) {
            return response()->json([]);
        }

        $query = \App\Models\ChatMessage::where(function($q) use ($userId, $guestToken) {
            if ($userId) {
                $q->where('user_id', $userId);
            } else {
                $q->where('guest_token', $guestToken);
            }
        });

        if ($userId) {
            $query->where('created_at', '>=', now()->subDays(30));
        } else {
            $query->where('created_at', '>=', now()->subHours(24));
        }

        return response()->json($query->orderBy('created_at', 'asc')->get());
    }

    /**
     * Gửi tin nhắn và nhận phản hồi từ AI
     */
    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => ['required', 'string', 'max:500'],
        ]);

        $userMessage = $request->input('message');
        $apiKey = config('services.gemini.api_key');

        $userId = auth()->id();
        $guestToken = $request->cookie('guest_chat_token');
        $cookieToQueue = null;

        if (!$userId && !$guestToken) {
            $guestToken = bin2hex(random_bytes(16));
            $cookieToQueue = cookie('guest_chat_token', $guestToken, 1440);
        }

        // 1. Lưu tin nhắn của User
        $userMessageDb = \App\Models\ChatMessage::create([
            'user_id'     => $userId,
            'guest_token' => $guestToken,
            'sender'      => 'user',
            'message'     => $userMessage,
        ]);

        if (empty($apiKey) || $apiKey === 'your-api-key-here') {
            return response()->json(['reply' => 'Chatbot chưa được cấu hình API Key.'], 500);
        }

        // 2. Lấy 10 tin nhắn gần nhất làm lịch sử
        $history = \App\Models\ChatMessage::where(function ($q) use ($userId, $guestToken) {
                if ($userId) {
                    $q->where('user_id', $userId);
                } else {
                    $q->where('guest_token', $guestToken);
                }
            })
            ->where('id', '<', $userMessageDb->id)
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get()
            ->reverse();

        // 3. Format dữ liệu cho Gemini (đảm bảo role luân phiên user/model)
        $contents = [];
        $lastRole = null;

        foreach ($history as $chat) {
            $role = ($chat->sender === 'user') ? 'user' : 'model';

            if ($role === $lastRole) {
                $lastIndex = count($contents) - 1;
                $contents[$lastIndex]['parts'][0]['text'] .= "\n" . $chat->message;
            } else {
                $contents[] = [
                    'role'  => $role,
                    'parts' => [['text' => $chat->message]],
                ];
                $lastRole = $role;
            }
        }

        // Thêm tin nhắn hiện tại của user
        if ($lastRole === 'user') {
            $lastIndex = count($contents) - 1;
            $contents[$lastIndex]['parts'][0]['text'] .= "\n" . $userMessage;
        } else {
            $contents[] = [
                'role'  => 'user',
                'parts' => [['text' => $userMessage]],
            ];
        }

        // 4. Lấy Context sách và tạo System Instruction
        $bookContext = $this->getBookContext($userMessage);
        $systemPrompt = "Bạn là trợ lý AI thông minh của 'Modtra Books' - nhà sách trực tuyến hàng đầu. "
            . "Nhiệm vụ: tư vấn sách, báo giá, hỗ trợ khách hàng thân thiện bằng tiếng Việt. "
            . "Chỉ dựa vào dữ liệu sách sau để tư vấn, không bịa thêm:\n\n{$bookContext}";

        $payload = [
            'system_instruction' => [
                'parts' => [['text' => $systemPrompt]],
            ],
            'contents'         => $contents,
            'generationConfig' => [
                'temperature'     => 0.7,
                'maxOutputTokens' => 800,
            ],
        ];

        // 5. Gọi Gemini API — thử lần lượt các model, chờ 3 giây giữa mỗi lần thử
        //    (Free tier bị giới hạn nghiêm ngặt, cần retry khi gặp "high demand" hoặc 429)
        $models = [
            'gemini-2.0-flash-lite',  // nhẹ nhất, ít bị rate limit
            'gemini-2.0-flash',
        ];

        $lastError = null;
        $maxRetries = 2; // Mỗi model thử tối đa 2 lần

        try {
            foreach ($models as $model) {
                for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
                    $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";

                    $response = Http::withoutVerifying()
                        ->timeout(45)
                        ->post($url, $payload);

                    if ($response->successful()) {
                        $data  = $response->json();
                        $reply = $data['candidates'][0]['content']['parts'][0]['text']
                            ?? 'Xin lỗi, tôi chưa hiểu ý bạn lắm.';

                        // Lưu phản hồi Bot
                        \App\Models\ChatMessage::create([
                            'user_id'     => $userId,
                            'guest_token' => $guestToken,
                            'sender'      => 'bot',
                            'message'     => $reply,
                        ]);

                        $jsonResponse = response()->json(['reply' => $reply]);
                        if ($cookieToQueue) {
                            $jsonResponse->cookie($cookieToQueue);
                        }
                        return $jsonResponse;
                    }

                    $errorBody = $response->json();
                    $lastError = $errorBody['error']['message'] ?? 'Unknown error';
                    $statusCode = $errorBody['error']['code'] ?? 0;
                    Log::warning("Gemini [{$model}] attempt {$attempt} failed ({$statusCode}): {$lastError}");

                    // Nếu lỗi 429 (rate limit) hoặc 503 (overloaded), chờ rồi thử lại
                    if (in_array($statusCode, [429, 503]) && $attempt < $maxRetries) {
                        sleep(4); // Chờ 4 giây trước khi thử lại
                        continue;
                    }

                    break; // Lỗi khác (400, 404...) → bỏ qua model này luôn
                }
            }

            // Tất cả model đều thất bại
            Log::error('All Gemini models failed. Last error: ' . $lastError);
            return response()->json([
                'reply' => 'Hệ thống AI đang quá tải do lượng truy cập cao. Vui lòng thử lại sau 1-2 phút nhé! 🙏',
            ], 500);

        } catch (\Exception $e) {
            Log::error('Chatbot Exception: ' . $e->getMessage());
            return response()->json([
                'reply' => 'Kết nối với máy chủ AI bị gián đoạn. Vui lòng thử lại.',
            ], 500);
        }
    }

    /**
     * Tìm sách liên quan (Đã fix logic tìm kiếm)
     */
    private function getBookContext(string $message): string
    {
        // Xóa các từ vặt vãnh tiếng Việt thường gặp để search chính xác hơn
        $stopWords = ['tôi', 'muốn', 'tìm', 'mua', 'quyển', 'cuốn', 'sách', 'có', 'không', 'cho', 'hỏi', 'về'];
        $cleanMessage = str_ireplace($stopWords, '', $message);
        $cleanMessage = trim(preg_replace('/\s+/', ' ', $cleanMessage));

        $query = Sach::with(['tacGia', 'theLoai']);

        // Tìm theo chuỗi sau khi đã lọc stop words (Tránh lỗi LIKE từng chữ)
        if (!empty($cleanMessage) && mb_strlen($cleanMessage) >= 3) {
            $query->where(function ($q) use ($cleanMessage) {
                $q->orWhere('tieu_de', 'LIKE', "%{$cleanMessage}%")
                  ->orWhere('mo_ta', 'LIKE', "%{$cleanMessage}%");
            });
            
            // Nếu muốn tìm thêm theo Thể loại:
            $query->orWhereHas('theLoai', function($q) use ($cleanMessage) {
                $q->where('ten_the_loai', 'LIKE', "%{$cleanMessage}%");
            });
        }

        $books = $query->take(5)->get();

        // Fallback: Nếu không tìm thấy, lấy 10 sách mới nhất
        if ($books->isEmpty()) {
            $books = Sach::with(['tacGia', 'theLoai'])
                ->orderByDesc('created_at')
                ->take(5)
                ->get();
        }

        if ($books->isEmpty()) {
            return 'Hiện tại cửa hàng chưa có sách nào.';
        }

        $context = "Sách hiện có:\n";

        foreach ($books as $book) {
            $author   = $book->tacGia->ten_tac_gia ?? 'Đang cập nhật';
            $category = $book->theLoai->ten_the_loai ?? 'Khác';
            $price    = number_format($book->gia_ban, 0, ',', '.') . ' VNĐ';
            
            // Sửa lại đoạn gọi hàm conHang() cho phù hợp với model của bạn (có thể là method hoặc attribute)
            // Giả sử con_hang là một column boolean trong DB:
            $stock = (isset($book->so_luong) && $book->so_luong > 0) ? 'Còn hàng' : 'Hết hàng'; 
            // Nếu bạn dùng method conHang() thật thì đổi lại thành: $book->conHang() ? 'Còn hàng' : 'Hết hàng';

            $context .= "- Tên sách: \"{$book->tieu_de}\" | Tác giả: {$author} | Thể loại: {$category} | Giá: {$price} | Trạng thái: {$stock}\n";
        }

        return $context;
    }
}