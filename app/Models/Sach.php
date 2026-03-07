<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sach extends Model
{
    use HasFactory;
    protected $table = 'sach';
    protected $fillable = [
        'tieu_de', 'tac_gia_id', 'nha_xuat_ban_id', 'nha_cung_cap_id',
        'the_loai_id', 'hinh_thuc_bia', 'mo_ta', 'file_anh_bia',
        'link_anh_bia', 'gia_ban', 'gia_goc', 'so_luong_ton',
        'isbn', 'nam_xuat_ban', 'so_trang','loai_sach'
    ];

    protected $casts = [
        'gia_ban' => 'decimal:2',
        'gia_goc' => 'decimal:2',
    ];

    public function tacGia()
    {
        return $this->belongsTo(TacGia::class, 'tac_gia_id');
    }

    public function nhaXuatBan()
    {
        return $this->belongsTo(NhaXuatBan::class, 'nha_xuat_ban_id');
    }

    public function nhaCungCap()
    {
        return $this->belongsTo(NhaCungCap::class, 'nha_cung_cap_id');
    }

    public function theLoai()
    {
        return $this->belongsTo(TheLoai::class, 'the_loai_id');
    }

    public function danhGias()
    {
        return $this->hasMany(DanhGia::class, 'sach_id');
    }

    // Tính trung bình sao tiện lợi
    public function trungBinhSao(): float
    {
        return $this->danhGias()->avg('so_sao') ?? 0;
    }

    // Kiểm tra còn hàng
    public function conHang(): bool
    {
        return $this->so_luong_ton > 0;
    }
}
