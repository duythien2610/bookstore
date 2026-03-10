<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DonHangChiTiet extends Model
{
    use HasFactory;
        protected $table = 'don_hang_chi_tiet';
    protected $fillable = ['don_hang_id', 'sach_id', 'so_luong', 'don_gia', 'thanh_tien'];

    public function donHang()
    {
        return $this->belongsTo(DonHang::class, 'don_hang_id');
    }

    public function sach()
    {
        return $this->belongsTo(Sach::class, 'sach_id');
    }
}
