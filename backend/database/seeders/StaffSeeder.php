<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Staff;
use App\Models\GymOwner;

class StaffSeeder extends Seeder
{
    public function run(): void
    {
        $gymOwners = GymOwner::all();
        $counter = 1;

        foreach ($gymOwners as $gymOwner) {
            Staff::create([
                'gym_owner_id' => $gymOwner->id,
                'name' => 'Recepcionista ' . $gymOwner->gym_name,
                'email' => 'recepcion' . $gymOwner->id . '@' . strtolower(str_replace(' ', '', $gymOwner->gym_name)) . '.com',
                'username' => 'staff' . $counter++,
                'password' => Hash::make('r0ch0*'),
                'active' => true,
            ]);

            Staff::create([
                'gym_owner_id' => $gymOwner->id,
                'name' => 'Entrenador ' . $gymOwner->gym_name,
                'email' => 'entrenador' . $gymOwner->id . '@' . strtolower(str_replace(' ', '', $gymOwner->gym_name)) . '.com',
                'username' => 'coach' . $counter++,
                'password' => Hash::make('C0z123'),
                'active' => true,
            ]);
        }
    }
}
