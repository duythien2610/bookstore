<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$sach = App\Models\Sach::first();
if (!$sach) {
    echo "No Sach found.\n";
    exit;
}

$html = view('pages.product-detail', [
    'sach' => $sach,
    'danhGias' => collect(),
    'diemTrungBinh' => 0,
    'phanPhoiSao' => [],
    'sachLienQuan' => collect(),
    'daGuiDanhGia' => false,
    'daMua' => false,
    'activeCoupon' => null
])->render();

if (strpos($html, 'id="lightbox"') !== false) {
    echo "Lightbox is present in the rendered HTML.\n";
} else {
    echo "LIGHTBOX IS MISSING in the rendered HTML!\n";
}

if (strpos($html, 'openLightbox') !== false) {
    echo "openLightbox function is present in the rendered HTML.\n";
} else {
    echo "function IS MISSING in the rendered HTML!\n";
}
