<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ActivityLog;
use App\Models\GymOwner;
use App\Models\SuperUser;
use App\Models\Staff;

class ActivityLogSeeder extends Seeder
{
    public function run(): void
    {
        $superUser = SuperUser::first();
        $gymOwners = GymOwner::all();

        foreach ($gymOwners as $gymOwner) {
            // Logs del SuperUser
            ActivityLog::create([
                'gym_owner_id' => $gymOwner->id,
                'user_type' => SuperUser::class,
                'user_id' => $superUser->id,
                'action' => 'create_gym',
                'description' => 'Creó nuevo gimnasio: ' . $gymOwner->gym_name,
            ]);

            // Logs del GymOwner
            ActivityLog::create([
                'gym_owner_id' => $gymOwner->id,
                'user_type' => GymOwner::class,
                'user_id' => $gymOwner->id,
                'action' => 'create_membership',
                'description' => 'Creó nueva membresía',
            ]);

            // Logs del Staff
            $staff = $gymOwner->staff->first();
            if ($staff) {
                ActivityLog::create([
                    'gym_owner_id' => $gymOwner->id,
                    'user_type' => Staff::class,
                    'user_id' => $staff->id,
                    'action' => 'check_in_client',
                    'description' => 'Registró entrada de cliente',
                ]);
            }
        }
    }
}