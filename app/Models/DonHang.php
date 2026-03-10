<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DonHang extends Model
{
    use HasFactory;
    protected $table = 'don_hang';
    protected $fillable = [
        'user_id', 'ma_giam_gia_id', 'ngay_dat', 'trang_thai',
        'tong_tien', 'dia_chi_giao', 'phuong_thuc_tt',
        'trang_thai_tt', 'ghi_chu'
    ];

    protected $casts = [
        'ngay_dat' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function maGiamGia()
    {
        return $this->belongsTo(MaGiamGia::class, 'ma_giam_gia_id');
    }

    public function chiTiets()
    {
        return $this->hasMany(DonHangChiTiet::class, 'don_hang_id');
    }
}
