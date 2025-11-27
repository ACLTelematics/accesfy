<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Backup;
use App\Models\GymOwner;
use App\Models\SuperUser;

class BackupSeeder extends Seeder
{
    public function run(): void
    {
        $superUser = SuperUser::first();
        $gymOwners = GymOwner::all();

        foreach ($gymOwners as $gymOwner) {
            // Backup creado por SuperUser
            Backup::create([
                'gym_owner_id' => $gymOwner->id,
                'created_by' => $superUser->id,
                'file_path' => 'backups/gym_' . $gymOwner->id . '_' . now()->format('Y-m-d_H-i-s') . '.sql',
                'restorable' => true,
                'is_applied' => false,
                'applied_by' => null,
                'applied_at' => null,
            ]);

            // Backup aplicado
            Backup::create([
                'gym_owner_id' => $gymOwner->id,
                'created_by' => $superUser->id,
                'file_path' => 'backups/gym_' . $gymOwner->id . '_' . now()->subDays(1)->format('Y-m-d_H-i-s') . '.sql',
                'restorable' => true,
                'is_applied' => true,
                'applied_by' => $superUser->id,
                'applied_at' => now()->subDays(1),
            ]);
        }
    }
}