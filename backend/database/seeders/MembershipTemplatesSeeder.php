<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Membership;
use App\Models\GymOwner;

class MembershipTemplatesSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Obtener todos los GymOwners
        $gymOwners = GymOwner::all();

        // Plantillas de membresías predefinidas
        $templates = [
            [
                'type' => 'visit',
                'duration_days' => 1,
                'price' => 0,
                'description' => 'Pago por visita individual',
                'daily_payment' => true,
                'active' => false,
            ],
            [
                'type' => 'weekly',
                'duration_days' => 7,
                'price' => 0,
                'description' => 'Membresía semanal',
                'daily_payment' => false,
                'active' => false,
            ],
            [
                'type' => 'biweekly',
                'duration_days' => 15,
                'price' => 0,
                'description' => 'Membresía quincenal',
                'daily_payment' => false,
                'active' => false,
            ],
            [
                'type' => 'individual',
                'duration_days' => 30,
                'price' => 0,
                'description' => 'Membresía mensual individual',
                'daily_payment' => false,
                'active' => true, // ✅ Activada por defecto
            ],
            [
                'type' => 'couple',
                'duration_days' => 30,
                'price' => 0,
                'description' => 'Membresía mensual para parejas',
                'daily_payment' => false,
                'active' => false,
            ],
            [
                'type' => 'student',
                'duration_days' => 30,
                'price' => 0,
                'description' => 'Membresía mensual para estudiantes',
                'daily_payment' => false,
                'active' => false,
            ],
            [
                'type' => 'quarterly',
                'duration_days' => 90,
                'price' => 0,
                'description' => 'Membresía trimestral',
                'daily_payment' => false,
                'active' => false,
            ],
            [
                'type' => 'semiannual',
                'duration_days' => 180,
                'price' => 0,
                'description' => 'Membresía semestral',
                'daily_payment' => false,
                'active' => false,
            ],
            [
                'type' => 'annual',
                'duration_days' => 365,
                'price' => 0,
                'description' => 'Membresía anual',
                'daily_payment' => false,
                'active' => false,
            ],
        ];

        // Crear plantillas para cada GymOwner
        foreach ($gymOwners as $gymOwner) {
            foreach ($templates as $template) {
                // Verificar si ya existe esta membresía para este gym
                $exists = Membership::where('gym_owner_id', $gymOwner->id)
                    ->where('type', $template['type'])
                    ->exists();

                if (!$exists) {
                    Membership::create([
                        'gym_owner_id' => $gymOwner->id,
                        'type' => $template['type'],
                        'duration_days' => $template['duration_days'],
                        'price' => $template['price'],
                        'description' => $template['description'],
                        'daily_payment' => $template['daily_payment'],
                        'active' => $template['active'],
                    ]);
                }
            }
        }

        $this->command->info('✅ Plantillas de membresías creadas para todos los gimnasios.');
    }
}
