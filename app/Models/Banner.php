<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    use HasFactory;
    protected $table = 'banners';
    protected $fillable = [
        'tieu_de', 'mo_ta', 'duong_dan_anh', 'link_anh',
        'lien_ket', 'vi_tri', 'thu_tu', 'trang_thai',
    ];

    protected $casts = [
        'trang_thai' => 'boolean',
    ];

    // Lấy src ảnh (ưu tiên file upload, fallback link ngoài)
    public function getAnhSrcAttribute(): ?string
    {
        if ($this->duong_dan_anh) {
            return asset('uploads/banners/' . $this->duong_dan_anh);
        }
        return $this->link_anh ?: null;
    }
}
