<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'users';

    protected $fillable = [
        'ho_ten',
        'email',
        'password',         
        'so_dien_thoai',
        'dia_chi',
        'role_id',
        'trang_thai',
        'avatar',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',        
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'trang_thai'        => 'boolean',
    ];

    // ===== Relationships =====
    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function gioHang()
    {
        return $this->hasOne(GioHang::class, 'user_id');
    }

    public function donHangs()
    {
        return $this->hasMany(DonHang::class, 'user_id');
    }

    public function danhGias()
    {
        return $this->hasMany(DanhGia::class, 'user_id');
    }

    // ===== Helper =====
    public function isAdmin(): bool
    {
        return $this->role->ten_vai_tro === 'admin';
    }
}
