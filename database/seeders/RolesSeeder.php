<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesSeeder extends Seeder
{
    public function run()
    {
        DB::table('roles')->insert([
            ['id' => 1, 'ten_vai_tro' => 'admin', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'ten_vai_tro' => 'user',  'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
