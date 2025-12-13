<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\GymOwner;
use App\Models\SuperUser;

class GymOwnerSeeder extends Seeder
{
    public function run(): void
    {
        $superUser = SuperUser::first();

        GymOwner::create([
            'super_user_id' => $superUser->id,
            'name' => 'rochosgym',
            'email' => 'bella@rochosgym.com',
            'username' => 'bellagym',
            'password' => Hash::make('admin123'),
            'active' => true,
            'activated_until' => now()->addMonths(12),
            'gym_name' => 'Rochos Muscle Gym',
        ]);

        GymOwner::create([
            'super_user_id' => $superUser->id,
            'name' => 'María López',
            'email' => 'maria@gimnasio2.com',
            'username' => 'mariagym',
            'password' => Hash::make('password123'),
            'active' => true,
            'activated_until' => now()->addMonths(6),
            'gym_name' => 'Power Gym',
        ]);

        GymOwner::create([
            'super_user_id' => $superUser->id,
            'name' => 'Juan Pérez',
            'email' => 'juan@gimnasio3.com',
            'username' => 'juangym',
            'password' => Hash::make('password123'),
            'active' => true,
            'activated_until' => now()->addMonths(3),
            'gym_name' => 'CrossFit Zone',
        ]);
    }
}
