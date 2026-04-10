<?php

namespace App\Services;

use RuntimeException;
use Illuminate\Support\Facades\Http;

class GeminiService
{
    /**
     * Gọi Gemini API để sinh câu trả lời từ một prompt.
     */
    public function generateReply(string $prompt): string
    {
        // Lấy cấu hình từ config/services.php
        $apiKey  = (string) config('services.gemini.api_key', '');
        $model   = (string) config('services.gemini.model', '');
        $baseUrl = rtrim((string) config('services.gemini.base_url', ''), '/');
        $timeout = max(1, (int) config('services.gemini.timeout', 20));

        // Kiểm tra tham số bắt buộc
        if ($apiKey === '') {
            throw new RuntimeException('Thiếu GEMINI_API_KEY trong .env');
        }
        if ($model === '') {
            throw new RuntimeException('Thiếu GEMINI_MODEL trong .env');
        }
        if ($baseUrl === '') {
            throw new RuntimeException('Thiếu GEMINI_BASE_URL trong .env');
        }

        // Gửi request POST tới Gemini API
        // BUG FIX 1: 'parts' phải là mảng của object [['text'=>...]], không phải ['text'=>...]
        // BUG FIX 2: key đúng là 'generationConfig', không phải 'generateConfig'
        $response = Http::acceptJson()
            ->asJson()
            ->timeout($timeout)
            ->withQueryParameters(['key' => $apiKey])
            ->post("{$baseUrl}/models/{$model}:generateContent", [
                'contents' => [
                    [
                        'role'  => 'user',
                        // ✅ Đúng: parts là array of objects
                        'parts' => [['text' => $prompt]],
                    ],
                ],
                // ✅ Đúng: 'generationConfig' (không phải 'generateConfig')
                'generationConfig' => [
                    'temperature'     => 0.4,
                    'maxOutputTokens' => 800,
                ],
            ]);

        // Nếu HTTP lỗi thì ném exception
        $response->throw();

        // BUG FIX 3: Đúng key là 'candidates', không phải 'cadidates'
        $candidates = $response->json('candidates', []);

        if (! is_array($candidates)) {
            throw new RuntimeException('Payload từ Gemini trả về không hợp lệ');
        }

        foreach ($candidates as $candidate) {
            $parts = data_get($candidate, 'content.parts', []);

            // BUG FIX 4: Kiểm tra $parts (không phải $part chưa tồn tại)
            //            Bỏ qua nếu parts KHÔNG phải mảng
            if (! is_array($parts)) {
                continue;
            }

            // BUG FIX 5: Dùng biến $piece để tránh xung đột tên với mảng $text
            $text = [];
            foreach ($parts as $part) {
                $piece = trim((string) data_get($part, 'text', ''));
                if ($piece !== '') {
                    $text[] = $piece;
                }
            }

            if ($text !== []) {
                return implode("\n", $text);
            }
        }

        throw new RuntimeException('Gemini không trả về nội dung hợp lệ');
    }
}