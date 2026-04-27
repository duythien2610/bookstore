<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(Illuminate\Http\Request::capture());

$apiKey = config('services.gemini.api_key');
$models = ['gemini-1.5-flash', 'gemini-2.5-flash', 'gemini-flash-latest'];

foreach ($models as $model) {
    echo "--- Testing MODEL: $model ---\n";
    $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";
    $payload = [
        'contents' => [
            ['role' => 'user', 'parts' => [['text' => 'Xin chào']]]
        ]
    ];
    $res = \Illuminate\Support\Facades\Http::withoutVerifying()->post($url, $payload);
    echo "Status: " . $res->status() . "\n";
    if ($res->status() == 429) {
        echo "429: " . ($res->json('error.message') ?? 'Unknown') . "\n";
    } else if ($res->status() != 200) {
        echo "Error: " . $res->body() . "\n";
    } else {
        echo "Success!\n";
    }
}
