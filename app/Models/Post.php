<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'category',
        'image',
        'content',
        'status',
        'views',
        'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
