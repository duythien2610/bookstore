<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;
    protected $table = 'roles';
    protected $fillable = ['ten_vai_tro'];

    public function users()
    {
        return $this->hasMany(User::class, 'role_id');
    }
}
