<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaGiamGia extends Model
{
    use HasFactory;
    protected $table = 'ma_giam_gia';
    protected $fillable = [
        'ma_code', 'loai', 'gia_tri',
        'ngay_het_han', 'so_luong', 'da_dung', 'trang_thai',
        'pham_vi', 'the_loai_ids', 'sach_ids',
        'dieu_kien_tai_khoan', 'don_hang_toi_thieu',
    ];

    protected $casts = [
        'ngay_het_han'       => 'date',
        'the_loai_ids'       => 'array',
        'sach_ids'           => 'array',
        'don_hang_toi_thieu' => 'decimal:2',
    ];

    public function donHangs()
    {
        return $this->hasMany(DonHang::class, 'ma_giam_gia_id');
    }
}
