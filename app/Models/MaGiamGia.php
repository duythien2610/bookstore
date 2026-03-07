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
        'ngay_het_han', 'so_luong', 'da_dung', 'trang_thai'
    ];

    protected $casts = [
        'ngay_het_han' => 'date',
    ];

    public function donHangs()
    {
        return $this->hasMany(DonHang::class, 'ma_giam_gia_id');
    }
}
