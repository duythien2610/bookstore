<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GioHangChiTiet extends Model
{
    use HasFactory;
    protected $table = 'gio_hang_chi_tiet';
    protected $fillable = ['gio_hang_id', 'sach_id', 'so_luong', 'don_gia', 'thanh_tien'];

    public function gioHang()
    {
        return $this->belongsTo(GioHang::class, 'gio_hang_id');
    }

    public function sach()
    {
        return $this->belongsTo(Sach::class, 'sach_id');
    }
}
