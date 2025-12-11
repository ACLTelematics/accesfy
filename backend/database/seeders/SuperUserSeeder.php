<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\SuperUser;

class SuperUserSeeder extends Seeder
{
    public function run(): void
    {
        SuperUser::create([
            'name' => 'Abigail',
            'username' => 'acanche',  // ← AGREGAR
            'email' => 'admin@accesfy.com',
            'password' => Hash::make('@050554'),
            'active' => true,
        ]);

        SuperUser::create([
            'name' => 'Administrador',
            'username' => 'admin',  // ← AGREGAR
            'email' => 'support@accesfy.com',
            'password' => Hash::make('0554'),
            'active' => true,
        ]);
    }
}
