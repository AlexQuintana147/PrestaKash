<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('roles')->insert([
            ['id' => 1, 'name' => 'admin',      'label' => 'Admin',      'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'name' => 'trabajador',  'label' => 'Trabajador', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'name' => 'usuario',     'label' => 'Usuario',    'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
