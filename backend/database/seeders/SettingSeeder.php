<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;
use App\Models\GymOwner;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $gymOwners = GymOwner::all();

        foreach ($gymOwners as $gymOwner) {
            // Configuración de horarios
            Setting::create([
                'gym_owner_id' => $gymOwner->id,
                'key' => 'opening_time',
                'value' => '06:00',
            ]);

            Setting::create([
                'gym_owner_id' => $gymOwner->id,
                'key' => 'closing_time',
                'value' => '22:00',
            ]);

            // Configuración de moneda
            Setting::create([
                'gym_owner_id' => $gymOwner->id,
                'key' => 'currency',
                'value' => 'USD',
            ]);

            // Configuración de idioma
            Setting::create([
                'gym_owner_id' => $gymOwner->id,
                'key' => 'language',
                'value' => 'es',
            ]);

            // Configuración de notificaciones
            Setting::create([
                'gym_owner_id' => $gymOwner->id,
                'key' => 'send_notifications',
                'value' => 'true',
            ]);

            // Configuración de biometría
            Setting::create([
                'gym_owner_id' => $gymOwner->id,
                'key' => 'biometric_enabled',
                'value' => 'true',
            ]);
        }
    }
}