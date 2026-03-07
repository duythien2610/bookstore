<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NhaCungCap extends Model
{
    use HasFactory;
    protected $table = 'nha_cung_cap';
    protected $fillable = ['ten_ncc'];

    public function sachs()
    {
        return $this->hasMany(Sach::class, 'nha_cung_cap_id');
    }
}
