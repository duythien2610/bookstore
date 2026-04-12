<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class DonHang extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'don_hang';
    protected $fillable = [
        'user_id', 'ma_giam_gia_id', 'ngay_dat', 'trang_thai', 'payos_order_code',
        'tong_tien', 'giam_gia', 'phi_van_chuyen', 'thanh_toan',
        'dia_chi_giao', 'phuong_thuc_tt', 'trang_thai_tt', 'ghi_chu',
        'ho_ten', 'so_dien_thoai',
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

    // Helper: label trạng thái
    public function getTrangThaiLabelAttribute(): string
    {
        return match ($this->trang_thai) {
            'cho_thanh_toan' => 'Chờ thanh toán',
            'cho_xac_nhan'   => 'Chờ xác nhận',
            'dang_xu_ly'     => 'Đang xử lý',
            'dang_giao'      => 'Đang giao hàng',
            'da_giao'        => 'Đã giao hàng',
            'huy'            => 'Đã hủy',
            default          => ucfirst($this->trang_thai),
        };
    }

    public function getTrangThaiColorAttribute(): string
    {
        return match ($this->trang_thai) {
            'cho_thanh_toan' => 'badge-warning',
            'cho_xac_nhan'   => 'badge-info',
            'dang_xu_ly'     => 'badge-primary',
            'dang_giao'      => 'badge-info',
            'da_giao'        => 'badge-success',
            'huy'            => 'badge-danger',
            default          => 'badge-secondary',
        };
    }
}
