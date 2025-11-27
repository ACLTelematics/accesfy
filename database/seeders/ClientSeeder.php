<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Client;
use App\Models\GymOwner;
use App\Models\Membership;

class ClientSeeder extends Seeder
{
    public function run(): void
    {
        $gymOwners = GymOwner::with('memberships')->get();

        foreach ($gymOwners as $gymOwner) {
            $memberships = $gymOwner->memberships;

            // Cliente 1 - Individual
            Client::create([
                'gym_owner_id' => $gymOwner->id,
                'name' => 'Juan Martínez',
                'email' => 'juan.martinez@email.com',
                'phone' => '555-0101',
                'active' => true,
                'membership_expires' => now()->addMonths(1),
                'membership_id' => $memberships->where('type', 'individual')->first()->id,
                'is_couple' => false,
                'related_client_id' => null,
                'fingerprint_hash' => null,
                'pin_hash' => null,
                'biometric_enabled' => false,
            ]);

            // Cliente 2 - Individual
            $client2 = Client::create([
                'gym_owner_id' => $gymOwner->id,
                'name' => 'Ana García',
                'email' => 'ana.garcia@email.com',
                'phone' => '555-0102',
                'active' => true,
                'membership_expires' => now()->addMonths(2),
                'membership_id' => $memberships->where('type', 'individual')->first()->id,
                'is_couple' => false,
                'related_client_id' => null,
                'fingerprint_hash' => null,
                'pin_hash' => null,
                'biometric_enabled' => false,
            ]);

            // Cliente 3 - Pareja (vinculado a Cliente 2)
            Client::create([
                'gym_owner_id' => $gymOwner->id,
                'name' => 'Luis García',
                'email' => 'luis.garcia@email.com',
                'phone' => '555-0103',
                'active' => true,
                'membership_expires' => now()->addMonths(2),
                'membership_id' => $memberships->where('type', 'couple')->first()->id,
                'is_couple' => true,
                'related_client_id' => $client2->id,
                'fingerprint_hash' => null,
                'pin_hash' => null,
                'biometric_enabled' => false,
            ]);

            // Cliente 4 - Estudiante
            Client::create([
                'gym_owner_id' => $gymOwner->id,
                'name' => 'Carlos López',
                'email' => 'carlos.lopez@email.com',
                'phone' => '555-0104',
                'active' => true,
                'membership_expires' => now()->addMonths(3),
                'membership_id' => $memberships->where('type', 'student')->first()->id,
                'is_couple' => false,
                'related_client_id' => null,
                'fingerprint_hash' => null,
                'pin_hash' => null,
                'biometric_enabled' => false,
            ]);

            // Cliente 5 - Por Visita
            Client::create([
                'gym_owner_id' => $gymOwner->id,
                'name' => 'María Rodríguez',
                'email' => 'maria.rodriguez@email.com',
                'phone' => '555-0105',
                'active' => true,
                'membership_expires' => now()->addDays(7),
                'membership_id' => $memberships->where('type', 'visit')->first()->id,
                'is_couple' => false,
                'related_client_id' => null,
                'fingerprint_hash' => null,
                'pin_hash' => null,
                'biometric_enabled' => false,
            ]);
        }
    }
}