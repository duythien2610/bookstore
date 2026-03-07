<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TacGia extends Model
{
    use HasFactory;
    protected $table = 'tac_gia';
    protected $fillable = ['ten_tac_gia'];

    public function sachs()
    {
        return $this->hasMany(Sach::class, 'tac_gia_id');
    }
}
