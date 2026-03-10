<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DanhGia extends Model
{
    use HasFactory;
    protected $table = 'danh_gia';
    protected $fillable = ['user_id', 'sach_id', 'so_sao', 'tieu_de', 'binh_luan', 'trang_thai'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function sach()
    {
        return $this->belongsTo(Sach::class, 'sach_id');
    }
}
