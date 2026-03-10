<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TheLoai extends Model
{
    use HasFactory;
    protected $table = 'the_loai';
    protected $fillable = ['parent_id', 'ten_the_loai'];

    // Danh mục cha
    public function parent()
    {
        return $this->belongsTo(TheLoai::class, 'parent_id');
    }

    // Danh mục con
    public function children()
    {
        return $this->hasMany(TheLoai::class, 'parent_id');
    }

    // Sách thuộc thể loại này
    public function sachs()
    {
        return $this->hasMany(Sach::class, 'the_loai_id');
    }

    // Chỉ lấy danh mục cha (parent_id = null)
    public function scopeCha($query)
    {
        return $query->whereNull('parent_id');
    }

    // Chỉ lấy danh mục con
    public function scopeCon($query)
    {
        return $query->whereNotNull('parent_id');
    }
}
