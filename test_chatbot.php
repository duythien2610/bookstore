<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$req = Illuminate\Http\Request::create('/chatbot/send', 'POST', ['message' => 'Tôi muốn tìm một cuốn sách về kinh tế']);
$controller = app(\App\Http\Controllers\ChatbotController::class);
$res = $controller->sendMessage($req);
echo "Result:\n";
echo $res->getContent() . "\n";
