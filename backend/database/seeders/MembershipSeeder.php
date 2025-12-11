<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Membership;
use App\Models\GymOwner;

class MembershipSeeder extends Seeder
{
    public function run(): void
    {
        $gymOwners = GymOwner::all();

        foreach ($gymOwners as $gymOwner) {
            // Membresía Individual Mensual
            Membership::create([
                'gym_owner_id' => $gymOwner->id,
                'type' => 'individual',
                'price' => 49.99,
                'description' => 'Acceso ilimitado 1 mes',
                'daily_payment' => false,
                'active' => true,
            ]);

            // Membresía Individual Diaria
            Membership::create([
                'gym_owner_id' => $gymOwner->id,
                'type' => 'individual',
                'price' => 5.00,
                'description' => 'Acceso 1 día',
                'daily_payment' => true,
                'active' => true,
            ]);

            // Membresía Pareja Mensual
            Membership::create([
                'gym_owner_id' => $gymOwner->id,
                'type' => 'couple',
                'price' => 79.99,
                'description' => 'Acceso ilimitado para 2 personas 1 mes',
                'daily_payment' => false,
                'active' => true,
            ]);

            // Membresía Estudiante Mensual
            Membership::create([
                'gym_owner_id' => $gymOwner->id,
                'type' => 'student',
                'price' => 29.99,
                'description' => 'Acceso estudiante con descuento 1 mes',
                'daily_payment' => false,
                'active' => true,
            ]);

            // Membresía Por Visita
            Membership::create([
                'gym_owner_id' => $gymOwner->id,
                'type' => 'visit',
                'price' => 8.00,
                'description' => 'Pago por visita',
                'daily_payment' => false,
                'active' => true,
            ]);

            // Membresía Personalizada
            Membership::create([
                'gym_owner_id' => $gymOwner->id,
                'type' => 'custom',
                'price' => 99.99,
                'description' => 'Plan personalizado con entrenador',
                'daily_payment' => false,
                'active' => true,
            ]);
        }
    }
}