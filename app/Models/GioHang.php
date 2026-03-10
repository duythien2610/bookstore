<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GioHang extends Model
{
    use HasFactory;
    protected $table = 'gio_hang';
    protected $fillable = ['user_id', 'trang_thai', 'tong_tien'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function chiTiets()
    {
        return $this->hasMany(GioHangChiTiet::class, 'gio_hang_id');
    }
}
