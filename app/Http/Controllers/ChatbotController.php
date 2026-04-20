<?php

namespace App\Http\Controllers;

use App\Models\Sach;
use App\Models\TheLoai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;           // ✅ BUG FIX: import Str facade cho normalizeText

class ChatbotController extends Controller
{
    // ─── __invoke: Delegate sang sendMessage (dùng khi route trỏ thẳng vào Controller) ─────
    public function __invoke(Request $request)
    {
        return $this->sendMessage($request);
    }

    // ─── Lấy lịch sử chat ────────────────────────────────────────────────────────────────────
    public function fetchMessage(Request $request)
    {
        $userId     = auth()->id();
        $guestToken = $request->cookie('guest_chat_token');

        if (! $userId && ! $guestToken) {
            return response()->json([]);
        }

        $query = \App\Models\ChatMessage::where(function ($q) use ($userId, $guestToken) {
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

    // ─── Gửi tin nhắn & nhận phản hồi từ AI ─────────────────────────────────────────────────
    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => ['required', 'string', 'max:500'],
        ]);

        $userMessage = $request->input('message');
        $apiKey      = config('services.gemini.api_key');

        $userId         = auth()->id();
        $guestToken     = $request->cookie('guest_chat_token');
        $cookieToQueue  = null;

        if (! $userId && ! $guestToken) {
            $guestToken    = bin2hex(random_bytes(16));
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

        // 3. Format dữ liệu cho Gemini (role phải luân phiên user/model)
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

        // 4. Lấy Context sách & FAQ
        $bookContext  = $this->getBookContext($userMessage);
        $faqContext   = $this->getFAQContext();

        $systemPrompt = "Bạn là trợ lý AI thông minh của 'Bookverse' - nhà sách trực tuyến hàng đầu. "
            . "Nhiệm vụ: tư vấn sách, báo giá, hỗ trợ khách hàng thân thiện bằng tiếng Việt. "
            . "Sử dụng thông tin sau để trả lời các câu hỏi về nhà sách:\n{$faqContext}\n\n"
            . "Khi giới thiệu sách, hãy LUÔN LUÔN đính kèm link sản phẩm được cung cấp trong context dưới định dạng Markdown [Tên sách](Link) để khách hàng có thể click vào xem chi tiết. "
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

        // 5. Gọi Gemini API — thử lần lượt các model, retry khi 429/503
        // ✅ Dùng model từ config/.env trước, fallback lần lượt sang model khác còn hoạt động
        //    Danh sách đã kiểm tra thực tế với API key hiện tại:
        //    gemini-2.5-flash-lite ✅ | gemini-flash-latest ✅ | gemini-flash-lite-latest ✅
        $configModel = config('services.gemini.model', 'gemini-2.5-flash-lite');
        $models = array_values(array_unique(array_filter([
            $configModel,
            'gemini-flash-latest',
            'gemini-flash-lite-latest',
        ])));
        $lastError  = null;
        $maxRetries = 2;

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

                    $errorBody  = $response->json();
                    $lastError  = $errorBody['error']['message'] ?? 'Unknown error';
                    $statusCode = $errorBody['error']['code'] ?? 0;
                    Log::warning("Gemini [{$model}] attempt {$attempt} failed ({$statusCode}): {$lastError}");

                    if (in_array($statusCode, [429, 503]) && $attempt < $maxRetries) {
                        sleep(4);
                        continue;
                    }

                    break;
                }
            }

            Log::error('All Gemini models failed. Last error: ' . $lastError);
            return response()->json([
                'reply' => 'Hệ thống AI đang quá tải. Vui lòng thử lại sau 1-2 phút nhé! 🙏',
            ], 500);

        } catch (\Exception $e) {
            Log::error('Chatbot Exception: ' . $e->getMessage());
            return response()->json([
                'reply' => 'Kết nối với máy chủ AI bị gián đoạn. Vui lòng thử lại.',
            ], 500);
        }
    }

    // ─── Lấy context sách liên quan đến tin nhắn ─────────────────────────────────────────────
    private function getBookContext(string $message): string
    {
        $normalized   = $this->normalizeText($message);
        $stopWords    = ['tôi', 'muốn', 'tìm', 'mua', 'quyển', 'cuốn', 'sách', 'có', 'không', 'cho', 'hỏi', 'về'];
        $cleanMessage = trim(preg_replace('/\s+/', ' ', str_ireplace($stopWords, '', $message)));

        // 1. Kiểm tra intent 'Sách bán chạy'
        $isAskingBestSeller = preg_match('/\b(bán chạy|ban chay|hot|phổ biến|mua nhiều|top)\b/u', $normalized);

        $query = Sach::with(['tacGia', 'theLoai']);

        if ($isAskingBestSeller) {
            $books = Sach::mostSold(5)->get();
            $contextPrefix = "Sách bán chạy nhất hiện nay:\n";
        } else {
            if (! empty($cleanMessage) && mb_strlen($cleanMessage) >= 3) {
                $query->where(function ($q) use ($cleanMessage) {
                    $q->orWhere('tieu_de', 'LIKE', "%{$cleanMessage}%")
                      ->orWhere('mo_ta',   'LIKE', "%{$cleanMessage}%");
                })->orWhereHas('theLoai', function ($q) use ($cleanMessage) {
                    $q->where('ten_the_loai', 'LIKE', "%{$cleanMessage}%");
                });
            }
            $books = $query->take(5)->get();
            $contextPrefix = "Kết quả tìm kiếm phù hợp:\n";
        }

        if ($books->isEmpty()) {
            $books = Sach::with(['tacGia', 'theLoai'])
                ->orderByDesc('created_at')
                ->take(5)
                ->get();
            $contextPrefix = "Sách mới cập nhật:\n";
        }

        $context = $contextPrefix;
        foreach ($books as $book) {
            $author   = $book->tacGia->ten_tac_gia   ?? 'Đang cập nhật';
            $category = $book->theLoai->ten_the_loai ?? 'Khác';
            $price    = number_format((float)$book->gia_ban, 0, ',', '.') . ' VNĐ';
            $stock    = (isset($book->so_luong_ton) && $book->so_luong_ton > 0) ? 'Còn hàng' : 'Hết hàng';
            $url      = url("/products/{$book->id}");

            $context .= "- Tên: \"{$book->tieu_de}\" | Tác giả: {$author} | Thể loại: {$category} | Giá: {$price} | {$stock} | Link: {$url}\n";
        }

        return $context;
    }

    /**
     * Cung cấp thông tin chung về cửa hàng (FAQ)
     */
    private function getFAQContext(): string
    {
        return <<<EOD
- Địa chỉ: 123 Đường Sách, Quận 1, TP. Hồ Chí Minh.
- Giờ hoạt động: 08:00 - 21:00 (Tất cả các ngày trong tuần).
- Chính sách giao hàng: Miễn phí vận chuyển cho đơn hàng từ 250.000đ. Phí giao hàng nội thành Sài Gòn là 20.000đ, tỉnh khác là 35.000đ.
- Chính sách đổi trả: Chấp nhận đổi trả trong vòng 7 ngày kể từ khi nhận hàng nếu có lỗi in ấn hoặc hư hỏng do vận chuyển.
- Liên hệ hỗ trợ: Hotline 1900 xxxx hoặc email support@bookverse.vn.
EOD;
    }

    // ─── Các hàm helper hỗ trợ phân tích tin nhắn ────────────────────────────────────────────

    /**
     * Kiểm tra tin nhắn có phải hỏi về sản phẩm không.
     * Trả về true → có intent mua/tìm, false → câu xã giao thuần túy.
     */
    private function shouldSuggestProducts(string $message): bool
    {
        $normalized       = $this->normalizeText($message);
        $hasProductIntent = $this->hasProductIntent($normalized);
        $isCasual         = $this->isCasualConversation($normalized);

        // Nếu chỉ xã giao mà không có intent mua → không gợi ý sản phẩm
        if ($isCasual && ! $hasProductIntent) {
            return false;
        }

        return true;
    }

    /**
     * Tích điểm mức độ liên quan của sách với từ khoá.
     * BUG FIX: đổi type-hint Product→Sach, sửa tên biến $keywwords→$keywords, return $score
     */
    private function calculateProductScore(Sach $product, array $keywords): int
    {
        $name        = $this->normalizeText($product->tieu_de    ?? '');
        $slug        = $this->normalizeText($product->slug       ?? '');
        $category    = $this->normalizeText($product->theLoai->ten_the_loai ?? '');
        $description = $this->normalizeText($product->mo_ta      ?? '');

        $score = 0;

        foreach ($keywords as $keyword) {
            if ($keyword === '') {
                continue;
            }

            if (str_contains($name, $keyword) || str_contains($slug, $keyword)) {
                $score += 6;
                continue;
            }

            if (str_contains($category, $keyword)) {
                $score += 4;
                continue;
            }

            if (str_contains($description, $keyword)) {
                $score += 2;
                continue;
            }
        }

        // Bonus nếu nhiều keyword xuất hiện liền nhau
        if (count($keywords) >= 2) {
            $phrase = implode(' ', $keywords);
            if ($phrase !== '' && (str_contains($name, $phrase) || str_contains($description, $phrase))) {
                $score += 5;
            }
        }

        return $score; // ✅ BUG FIX: phải return $score
    }

    /**
     * Xây dựng danh sách sách phù hợp và text context để gửi cho AI.
     * BUG FIX: xử lý cả 2 nhánh (có/không có product intent) và trả về đầy đủ.
     */
    private function buildProductContext(string $message, bool $suggestProducts): array
    {
        if (! $suggestProducts) {
            return [
                'text'             => 'Tin nhắn là câu xã giao, không có danh sách gợi ý sản phẩm.',
                'matched_products' => [],
            ];
        }

        $keywords = $this->extractKeywords($message);
        $books    = Sach::with(['tacGia', 'theLoai'])->take(30)->get();

        $scored = [];
        foreach ($books as $book) {
            $score = $this->calculateProductScore($book, $keywords);
            if ($score > 0) {
                $scored[] = ['book' => $book, 'score' => $score];
            }
        }

        // Sắp xếp theo điểm giảm dần
        usort($scored, fn ($a, $b) => $b['score'] - $a['score']);
        $top = array_slice($scored, 0, 5);

        if (empty($top)) {
            return [
                'text'             => 'Không tìm thấy sách phù hợp với yêu cầu.',
                'matched_products' => [],
            ];
        }

        $text    = "Sách phù hợp:\n";
        $matched = [];
        foreach ($top as $item) {
            $book     = $item['book'];
            $author   = $book->tacGia->ten_tac_gia   ?? 'Đang cập nhật';
            $category = $book->theLoai->ten_the_loai ?? 'Khác';
            $price    = number_format($book->gia_ban, 0, ',', '.') . ' VNĐ';

            $text     .= "- {$book->tieu_de} | {$author} | {$category} | {$price}\n";
            $matched[] = $book->id;
        }

        return [
            'text'             => $text,
            'matched_products' => $matched,
        ];
    }

    /**
     * Trích xuất từ khoá từ tin nhắn để tìm kiếm sản phẩm.
     * BUG FIX: khởi tạo $keywords=[] trước vòng lặp để tránh undefined variable.
     */
    private function extractKeywords(string $message): array
    {
        preg_match_all('/[a-z0-9]{2,}/', $this->normalizeText($message), $matches);
        $rawTokens = $matches[0] ?? [];

        $stopWords = ['toi', 'muon', 'can', 'tim', 'cho', 've', 'cac', 'nhung', 'xin', 'admin', 'la', 'co', 'va', 'de'];

        $keywords = []; // ✅ BUG FIX: khởi tạo trước
        foreach ($rawTokens as $token) {
            if (in_array($token, $stopWords, true)) {
                continue;
            }
            $keywords[] = $token;
        }

        return array_values(array_slice(array_unique($keywords), 0, 8));
    }

    /**
     * Nhận diện user có ý định tìm/mua sản phẩm không.
     */
    private function hasProductIntent(string $normalizedMessage): bool
    {
        $patterns = [
            '/\b(mua|đặt mua|order|đặt hàng|chốt đơn|lấy ngay)\b/u',
            '/\b(tìm|tìm kiếm|search|kiếm|có bán|ở đâu bán)\b/u',
            '/\b(gợi ý|goi y|tư vấn|tu van|recommend|đề xuất)\b/u',
            '/\b(so sánh|so sanh|compare|chọn|chon|loại nào tốt|nên mua)\b/u',
            '/\b(có .* không|còn hàng không|giá bao nhiêu|bao nhiêu tiền)\b/u',
            '/\b(danh sách|list|các loại|mẫu nào|sản phẩm nào)\b/u',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $normalizedMessage)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Nhận diện tin nhắn xã giao / chào hỏi.
     */
    private function isCasualConversation(string $normalizedMessage): bool
    {
        $casualPatterns = [
            '/^xin chao\b/',
            '/^chao\b/',
            '/^hi\b/',
            '/^hello\b/',
            '/^cam on\b/',
            '/^co day khong\b/',
            '/^noi chuyen\b/',
            '/^alo\b/',
            '/^ban co nghe toi noi khong\b/',
            '/^co the tu van cho toi khong\b/',
            '/^co the giup cho toi khong\b/',
        ];

        foreach ($casualPatterns as $pattern) {
            if (preg_match($pattern, $normalizedMessage)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Chuẩn hoá tiếng Việt: bỏ dấu, lowercase, trim khoảng trắng.
     * BUG FIX: sửa mb_Strtklowwer → mb_strtolower, thêm use Str ở đầu file
     */
    private function normalizeText(?string $value): string
    {
        if ($value === null || $value === '') {
            return '';
        }

        // ✅ BUG FIX: mb_strtolower (không phải mb_Strtklowwer)
        $normalized = Str::ascii(mb_strtolower($value));

        return preg_replace('/\s+/', ' ', trim($normalized)) ?? '';
    }
}