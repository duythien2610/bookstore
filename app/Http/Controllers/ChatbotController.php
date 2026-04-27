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

        // — Kiểm tra cache cho câu hỏi giống nhau (giảm tải API) —
        $cacheKey = 'chatbot_reply_' . md5(mb_strtolower(trim($userMessage)));
        $cachedReply = \Illuminate\Support\Facades\Cache::get($cacheKey);
        if ($cachedReply) {
            \App\Models\ChatMessage::create(['user_id' => $userId, 'guest_token' => $guestToken, 'sender' => 'user', 'message' => $userMessage]);
            \App\Models\ChatMessage::create(['user_id' => $userId, 'guest_token' => $guestToken, 'sender' => 'bot',  'message' => $cachedReply]);
            $resp = response()->json(['reply' => $cachedReply]);
            if ($cookieToQueue) $resp->cookie($cookieToQueue);
            return $resp;
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

        $systemPrompt = <<<SYSTEM
## ROLE
Bạn là "Bookverse AI" — trợ lý bán hàng thông minh của Bookverse, nhà sách trực tuyến hàng đầu Việt Nam.
BẠN TUYỆT ĐỐI PHẢI TRẢ LỜI NGÔN NGỮ CHÍNH LÀ TIẾNG VIỆT TRONG MỌI TRƯỜNG HỢP, kể cả khi người dùng chat bằng Tiếng Anh.
QUY TẮC CỐT LÕI: Bạn PHẢI GIỮ NGUYÊN GỐC (không phiên dịch) cho các Tên Sách, Tên Tác Giả, và Thể Loại Sách bằng tiếng Anh hoặc tiếng nước ngoài (Ví dụ: "Harry Potter", "J.K. Rowling", "Self-help"). Tuyệt đối không cố gắng dịch tên riêng sang tiếng Việt.

Ưu tiên theo thứ tự:
  1. Trả lời chính xác, chỉ dựa vào dữ liệu được cung cấp trong <CONTEXT> và <STORE_INFO>
  2. Hỗ trợ người dùng tìm sách, theo dõi đơn hàng, giải quyết vấn đề
  3. Tuyệt đối không bịa đặt giá, tình trạng kho, chính sách — giảm thiểu hallucination tối đa

---

## INTENT CLASSIFICATION (xử lý nội tâm — KHÔNG hiển thị ra ngoài)
Trước khi soạn câu trả lời, phân loại tin nhắn vào một trong các nhóm sau:

  product_discovery   → tìm sách, gợi ý, so sánh / browsing, recommendations
  product_detail      → thông tin sách cụ thể: tác giả, thể loại, mô tả, giá
  inventory_check     → hỏi còn hàng không, bao giờ có thêm hàng
  order_status        → tra cứu đơn hàng, tình trạng giao hàng, thời gian nhận
  return_exchange     → chính sách đổi trả, hoàn tiền
  payment_issue       → vấn đề thanh toán, lỗi giao dịch, refund
  complaint           → khiếu nại, trải nghiệm tiêu cực
  general_faq         → phí vận chuyển, phương thức thanh toán, giờ làm việc
  out_of_scope        → không liên quan đến cửa hàng sách

Quy tắc:
  → CONFIDENCE thấp (ý định không rõ): đặt đúng MỘT câu hỏi làm rõ, không hỏi nhiều cùng lúc
  → INTENT = out_of_scope: nhẹ nhàng chuyển hướng về chủ đề nhà sách

ENTITY EXTRACTION (nội tâm):
  Nhận diện: tên sách, tác giả, thể loại, mã đơn hàng, khoảng giá, số lượng

---

## QUY TẮC GROUNDING
  - Mọi thông tin thực tế PHẢI lấy từ <CONTEXT> hoặc <STORE_INFO> bên dưới
  - Giá sách: chỉ báo nếu xuất hiện trong <CONTEXT>
  - Tình trạng kho: chỉ báo nếu có trong <CONTEXT>
  - Thời gian giao hàng / ETA: chỉ báo nếu có trong <STORE_INFO>
  - Nếu <CONTEXT> trống hoặc không liên quan → thừa nhận thành thật, KHÔNG bịa đặt

  Công thức khi không chắc:
    "Theo thông tin tôi có, [nhận định]. Để chắc chắn hơn, bạn có thể [hành động cụ thể]."

---

## ĐỊNH DẠNG PHẢN HỒI
  - Câu hỏi đơn giản: ≤ 3 câu
  - Khi gợi ý sách: TỐI ĐA 3 cuốn mỗi lần, mỗi cuốn 1-2 dòng. Đừng viết dài.
  - Khi giới thiệu sách: LUÔN đính kèm link Markdown [Tên sách](URL) để khách click xem chi tiết
  - So sánh / hướng dẫn nhiều bước: dùng bullet points ngắn gọn
  - Kết thúc bằng 1 câu hỏi mở ngắn hoặc CTA nhẹ nhàng
  - Tone: thân thiện, ngắn gọn, chuyên nghiệp
  - Khiếu nại: đồng cảm ngắn, đưa giải pháp ngay

---

## STORE_INFO (FAQ — nguồn duy nhất cho thông tin cửa hàng)
<STORE_INFO>
{$faqContext}
</STORE_INFO>

---

## CONTEXT (dữ liệu sách thực từ hệ thống — nguồn sự thật duy nhất để tư vấn sản phẩm)
<CONTEXT>
{$bookContext}
</CONTEXT>
SYSTEM;

        $payload = [
            'system_instruction' => [
                'parts' => [['text' => $systemPrompt]],
            ],
            'contents'         => $contents,
            'generationConfig' => [
                'temperature'     => 0.6,
                'maxOutputTokens' => 2000, // Cung cấp tới 2000 tokens (tương đương 4000 ký tự) để AI thoải mái diễn đạt mà không bao giờ bị cắt
            ],
        ];

        // 5. Gọi Gemini API — xoay vòng nhiều API Key và nhiều Model để tránh 429
        $configModel = config('services.gemini.model', 'gemini-flash-latest');
        $models = array_values(array_unique(array_filter([
            $configModel,
            'gemini-2.5-flash',
            'gemini-flash-latest',
            'gemini-1.5-flash',
            'gemini-1.5-flash-8b'
        ])));

        // Thu thập tất cả API keys có trong .env (kỳ thuật xoay vòng)
        $apiKeys = array_values(array_filter([
            config('services.gemini.api_key'),
            env('GEMINI_API_KEY_2'),
            env('GEMINI_API_KEY_3'),
        ]));

        $lastError = null;

        try {
            // Thử lần lượt từng key x khả dụng
            foreach ($apiKeys as $currentKey) {
                foreach ($models as $model) {
                    $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$currentKey}";

                    $response = Http::withoutVerifying()
                        ->timeout(30)
                        ->post($url, $payload);

                    if ($response->successful()) {
                        $data  = $response->json();
                        $reply = $data['candidates'][0]['content']['parts'][0]['text']
                            ?? 'Xin lỗi, tôi chưa hiểu ý bạn lắm.';

                        // Lưu cache 10 phút cho câu hỏi giống nhau
                        \Illuminate\Support\Facades\Cache::put($cacheKey, $reply, 600);

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
                    $statusCode = $errorBody['error']['code'] ?? $response->status();
                    Log::warning("Gemini [Key:***{$currentKey[strlen($currentKey)-4]}] [{$model}] ({$statusCode}): {$lastError}");

                    // Nếu 429 (Rate limit): dừng nhẫn model này, ngay lập tức sang model tiếp theo
                    // Không sleep giữa các model trong cùng key — chỉ sleep khi đã hết key
                    if ($statusCode != 429) {
                        // Lỗi khác (403, 500...) thì không cần thử tiếp
                        break;
                    }
                }

                // Nếu key này bị rate limit toàn bộ model, đợi 1s rồi sang key tiếp theo
                usleep(1000000);
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
        $normalized = $this->normalizeText($message);

        // ── 1. Best-seller intent ──────────────────────────────────────────
        if (preg_match('/\b(ban chay|ban chay nhat|hot|pho bien|mua nhieu|top|best.?sell|popular|nhieu nguoi mua|trending)\b/', $normalized)) {
            $books = Sach::mostSold(5)->with(['tacGia', 'theLoai'])->get();
            return $this->buildContextString($books, 'Sách bán chạy nhất hiện nay:');
        }

        // ── 1.2 Discount / Sale intent ─────────────────────────────────────
        if (preg_match('/\b(giam gia|khuyen mai|sale|discount|re hon|uu dai)\b/', $normalized)) {
            $books = Sach::whereColumn('gia_ban', '<', 'gia_goc')
                ->with(['tacGia', 'theLoai'])
                ->inRandomOrder()
                ->take(5)
                ->get();
            if ($books->isNotEmpty()) {
                return $this->buildContextString($books, 'Sách đang được giảm giá / khuyến mãi:');
            }
        }

        // ── 1.3 New Arrivals intent ────────────────────────────────────────
        if (preg_match('/\b(moi nhat|moi ra|sach moi|vua ra mat|latest|newest)\b/', $normalized)) {
            $books = Sach::with(['tacGia', 'theLoai'])->orderByDesc('created_at')->take(5)->get();
            return $this->buildContextString($books, 'Sách mới ra mắt cập nhật gần đây:');
        }

        // ── 1.4 Ngoại văn / Trong nước intent ───────────────────────────────
        if (preg_match('/\b(ngoai van|nuoc ngoai|tieng anh|quoc te)\b/', $normalized)) {
            $books = Sach::where('loai_sach', 'nuoc_ngoai')->with(['tacGia', 'theLoai'])->inRandomOrder()->take(5)->get();
            if ($books->isNotEmpty()) {
                 return $this->buildContextString($books, 'Gợi ý sách ngoại văn / quốc tế:');
            }
        }

        // ── 1.5 Target Price intent (Ví dụ: sách dưới 100k, rẻ hơn 50000) ───
        if (preg_match('/(duoi|re|nho hon|thap hon|under|below)\s*(\d+)\s*(k|ngan|vnd|d)?/i', $normalized, $matches)) {
            $price = intval($matches[2]);
            if (!empty($matches[3]) && in_array(strtolower($matches[3]), ['k', 'ngan'])) {
                $price *= 1000;
            } elseif ($price < 1000) {
                $price *= 1000;
            }
            $books = Sach::where('gia_ban', '<=', $price)->orderByDesc('gia_ban')->with(['tacGia', 'theLoai'])->take(5)->get();
            if ($books->isNotEmpty()) {
                return $this->buildContextString($books, "Sách có giá tầm dưới " . number_format($price) . "đ:");
            }
        }

        // ── 2. Extract language-agnostic tokens ────────────────────────────
        $tokens = $this->extractTokens($message);

        if (empty($tokens)) {
            return $this->buildContextString(
                Sach::with(['tacGia', 'theLoai'])->orderByDesc('created_at')->take(5)->get(),
                'Sách mới cập nhật:'
            );
        }

        // ── 3. Resolve category IDs & Author IDs ───────────────────────────
        $categoryIds = $this->findCategoryIds($tokens, $normalized);
        $authorIds   = $this->findAuthorIds($normalized);

        // ── 4. Fetch books from distinct sources to prevent 'take' truncation ───────
        $books = collect();

        // Fetch exactly by Author
        if (!empty($authorIds)) {
            $authorBooks = Sach::with(['tacGia', 'theLoai.parent'])
                ->whereIn('tac_gia_id', $authorIds)
                ->take(15)
                ->get();
            $books = $books->merge($authorBooks);
        }

        // Fetch exactly by Category
        if (!empty($categoryIds)) {
            $catBooks = Sach::with(['tacGia', 'theLoai.parent'])
                ->whereIn('the_loai_id', $categoryIds)
                ->take(15)
                ->get();
            $books = $books->merge($catBooks);
        }

        // Fetch by Title Tokens
        if (!empty($tokens)) {
            $tokenBooks = Sach::with(['tacGia', 'theLoai.parent'])
                ->where(function ($root) use ($tokens) {
                    foreach ($tokens as $t) {
                        $root->orWhere('tieu_de', 'LIKE', "%{$t['original']}%");
                        if ($t['normalized'] !== mb_strtolower($t['original'])) {
                            $root->orWhere('tieu_de', 'LIKE', "%{$t['normalized']}%");
                        }
                    }
                })
                ->take(20)
                ->get();
            $books = $books->merge($tokenBooks);
        }
        
        $books = $books->unique('id');

        // ── 5. Score & rank by per-token relevance ────────────────────────
        if ($books->count() > 1) {
            $books = $this->scoreAndRankBooks($books, $tokens, $normalized);
        }
        $books = $books->take(5);

        // ── 6. Fallback to newest books ───────────────────────────────────
        if ($books->isEmpty()) {
            return $this->buildContextString(
                Sach::with(['tacGia', 'theLoai'])->orderByDesc('created_at')->take(5)->get(),
                'Sách mới cập nhật:'
            );
        }

        return $this->buildContextString($books, 'Kết quả tìm kiếm phù hợp:');
    }

    /**
     * Extract tokens from user message — language-agnostic (Vietnamese + English).
     * Each token carries both its original Unicode form and its ASCII-normalized form
     * so we can query the DB with the original (diacritic-aware) while scoring with
     * the normalized (accent-stripped) form.
     *
     * @return array<int, array{original: string, normalized: string}>
     */
    private function extractTokens(string $message): array
    {
        // Vietnamese stop words (original + accent-stripped)
        $stopVi = [
            'tôi','toi','mình','minh','bạn','ban','ơi','oi',
            'muốn','muon','cần','can','tìm','tim','xem',
            'mua','quyển','quyen','cuốn','cuon','sách','sach',
            'có','co','không','khong','cho','hỏi','hoi','về','ve',
            'nhé','nhe','nha','nhỉ','nhi','thì','thi',
            'và','va','hoặc','hoac','hay','với','voi','của','cua',
            'là','la','theo','gợi','goi','ý','tư','tu','vấn','van',
            'những','nhung','các','cac','loại','loai','thể','the',
            'liên','lien','quan','một','mot','số','so','nhiều','nhieu',
            'được','duoc','cho','tôi','này','nay','kia','đó','do',
        ];
        // English stop words
        $stopEn = [
            'i','me','my','we','you','your','he','she','it','they','them',
            'want','need','find','look','looking','search','searching',
            'book','books','the','a','an','is','are','was','were','be',
            'for','about','on','in','at','to','of','by','with','from',
            'what','which','how','do','can','could','please','show',
            'me','some','any','give','have','get','help','suggest',
            'recommend','tell','know','like','more','good','best',
        ];
        $allStops = array_merge($stopVi, $stopEn);

        // Split on whitespace and common delimiters (preserve hyphens inside words)
        $parts = preg_split('/[\s,;\/\(\)\[\]\"\'\!\?]+/u', trim($message), -1, PREG_SPLIT_NO_EMPTY);

        $tokens = [];
        $seen   = [];

        foreach ($parts as $part) {
            // Strip leading/trailing punctuation but not internal hyphens
            $part = trim($part, '.:_');
            if (mb_strlen($part) < 2) {
                continue;
            }
            $normPart = $this->normalizeText($part);
            if ($normPart === '' || isset($seen[$normPart])) {
                continue;
            }
            // Skip if this is a stop word (check both original lower and normalized)
            if (in_array(mb_strtolower($part), $allStops, true)
                || in_array($normPart, $allStops, true)) {
                continue;
            }
            $seen[$normPart] = true;
            $tokens[] = ['original' => $part, 'normalized' => $normPart];
        }

        return $tokens;
    }

    /**
     * Find all TheLoai IDs that match any token, then expand parent categories
     * to include every descendant so a search for "Kinh Tế" returns books
     * filed under any child category like "Marketing - Bán Hàng".
     *
     * @param  array<int, array{original: string, normalized: string}> $tokens
     * @return int[]
     */
    private function findCategoryIds(array $tokens, string $normalizedMessage): array
    {
        // Lấy tất cả danh mục
        $allCats = \Illuminate\Support\Facades\Cache::remember('all_categories_chatbot', 3600, function() {
            return TheLoai::select('id', 'parent_id', 'ten_the_loai')->get();
        });

        $matchedIds = [];
        foreach ($allCats as $cat) {
            $catNorm = $this->normalizeText($cat->ten_the_loai);
            if ($catNorm === '') {
                continue;
            }

            // Entity extraction nguyên cụm (Ví dụ: "kinh tế" trong "tôi tìm sách kinh tế")
            if (str_contains($normalizedMessage, $catNorm)) {
                $matchedIds[] = (int) $cat->id;
                continue;
            }

            // Phân loại dính từ khóa (Fuzzy Matching):
            // Ví dụ: người dùng gõ "marketing", cần match vào "Sales & Marketing"
            // Ví dụ: người dùng gõ "kinh doanh", cần match "Tài chính - Kinh Doanh"
            $matchedTokens = 0;
            foreach ($tokens as $token) {
                if (mb_strlen($token['normalized']) >= 3 && str_contains($catNorm, $token['normalized'])) {
                    $matchedTokens++;
                }
            }

            if ($matchedTokens > 0) {
                $matchedIds[] = (int) $cat->id;
            }
        }

        if (empty($matchedIds)) {
            return [];
        }

        // Expand: for each matched ID, add all direct children
        // Để người dùng phân loại thể loại lớn (cha) vẫn nhận được sách thuộc thể loại con
        $expanded = $matchedIds;
        foreach ($matchedIds as $catId) {
            $childIds = $allCats->where('parent_id', $catId)->pluck('id')->map('intval')->all();
            $expanded = array_merge($expanded, $childIds);
        }

        return array_values(array_unique($expanded));
    }

    /**
     * Find all Author IDs matching the query message.
     */
    private function findAuthorIds(string $normalizedMessage): array
    {
        $allAuthors = \Illuminate\Support\Facades\Cache::remember('all_authors_chatbot', 3600, function() {
            return \App\Models\TacGia::select('id', 'ten_tac_gia')->get();
        });

        $matchedIds = [];
        foreach ($allAuthors as $author) {
            $authorNorm = $this->normalizeText($author->ten_tac_gia);
            if ($authorNorm === '') {
                continue;
            }

            // TÌM THEO THỰC THỂ NGUYÊN VẸN TRONG CÂU
            if (str_contains($normalizedMessage, $authorNorm)) {
                $matchedIds[] = (int) $author->id;
            }
        }
        return $matchedIds;
    }

    /**
     * Score books by per-token relevance and return sorted Collection.
     * Higher weight on title > author > category > description.
     *
     * @param  \Illuminate\Support\Collection|\Illuminate\Database\Eloquent\Collection $books
     * @param  array<int, array{original: string, normalized: string}> $tokens
     * @return \Illuminate\Support\Collection
     */
    private function scoreAndRankBooks($books, array $tokens, string $normalizedMessage)
    {
        $scored = $books->map(function ($book) use ($tokens, $normalizedMessage) {
            $titleNorm  = $this->normalizeText($book->tieu_de ?? '');
            $authorNorm = $this->normalizeText($book->tacGia->ten_tac_gia ?? '');
            $catNorm    = $this->normalizeText($book->theLoai->ten_the_loai ?? '');
            $parentNorm = $this->normalizeText(optional($book->theLoai->parent)->ten_the_loai ?? '');
            $descNorm   = $this->normalizeText(Str::limit(strip_tags($book->mo_ta ?? ''), 400));

            $score = 0;
            foreach ($tokens as $t) {
                $kw = $t['normalized'];
                if ($kw === '') {
                    continue;
                }
                if (str_contains($titleNorm,  $kw)) { $score += 10; }
                if (str_contains($authorNorm, $kw)) { $score += 8;  }
                if (str_contains($catNorm,    $kw)) { $score += 5;  }
                if (str_contains($parentNorm, $kw)) { $score += 3;  }
                if (str_contains($descNorm,   $kw)) { $score += 2;  }
            }

            // Phrase-level bonus: full normalized query appears verbatim in title
            if ($normalizedMessage !== '' && str_contains($titleNorm, $normalizedMessage)) {
                $score += 15;
            }

            $book->_chatbot_score = $score;
            return $book;
        });

        return $scored
            ->sortByDesc('_chatbot_score')
            ->filter(fn($b) => $b->_chatbot_score > 0)
            ->values();
    }

    /**
     * Format a collection of books into the context string sent to the AI.
     *
     * @param  \Illuminate\Support\Collection|\Illuminate\Database\Eloquent\Collection $books
     */
    private function buildContextString($books, string $prefix): string
    {
        $context = $prefix . "\n";
        foreach ($books as $book) {
            $author   = $book->tacGia->ten_tac_gia   ?? 'Đang cập nhật';
            $category = $book->theLoai->ten_the_loai ?? 'Khác';
            $price    = number_format((float) $book->gia_ban, 0, ',', '.') . ' VNĐ';
            $stock    = (($book->so_luong_ton ?? 0) > 0) ? 'Còn hàng' : 'Hết hàng';
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
     * Trích xuất từ khoá từ tin nhắn (backward-compatible, delegates to extractTokens).
     * Returns an array of normalized keyword strings.
     */
    private function extractKeywords(string $message): array
    {
        $tokens = $this->extractTokens($message);
        return array_values(array_slice(
            array_unique(array_column($tokens, 'normalized')),
            0, 8
        ));
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
     * Chuẩn hoá văn bản đa ngôn ngữ (Tiếng Việt + Tiếng Anh):
     *  1. Lowercase (Unicode-aware)
     *  2. Dashes/hyphens surrounding spaces → single space  (e.g. "Marketing - Bán Hàng" → "marketing ban hang")
     *  3. Str::ascii() → strip diacritics (ă→a, ơ→o, đ→d, é→e …)
     *  4. Collapse whitespace
     */
    private function normalizeText(?string $value): string
    {
        if ($value === null || $value === '') {
            return '';
        }

        // Step 1: lowercase
        $v = mb_strtolower(trim($value));
        // Step 2: replace " - " style separators with a space
        $v = preg_replace('/\s*[-–—]+\s*/', ' ', $v) ?? $v;
        // Step 3: strip diacritics → ASCII
        $v = Str::ascii($v);
        // Step 4: collapse runs of whitespace
        return preg_replace('/\s+/', ' ', trim($v)) ?? '';
    }
}