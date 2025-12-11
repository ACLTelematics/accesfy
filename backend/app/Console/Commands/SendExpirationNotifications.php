<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Client;
use App\Models\Notification;
use Carbon\Carbon;

class SendExpirationNotifications extends Command
{
    protected $signature = 'notifications:expiration';
    protected $description = 'Enviar notificaciones de membresías próximas a vencer';

    public function handle()
    {
        $today = Carbon::today();

        // Buscar clientes que expiran en 4, 3, 2 o 1 días
        for ($days = 4; $days >= 1; $days--) {
            $expirationDate = $today->copy()->addDays($days);

            $clients = Client::whereDate('membership_expires', $expirationDate)
                ->where('active', true)
                ->get();

            foreach ($clients as $client) {
                // Verificar si ya existe notificación para este día
                $exists = Notification::where('client_id', $client->id)
                    ->where('type', 'membership_expiring')
                    ->whereDate('created_at', $today)
                    ->exists();

                if (!$exists) {
                    Notification::create([
                        'client_id' => $client->id,
                        'type' => 'membership_expiring',
                        'title' => 'Membresía próxima a vencer',
                        'message' => "Tu membresía vence en {$days} día(s). Renueva pronto para seguir disfrutando.",
                        'is_read' => false,
                    ]);

                    $this->info("Notificación enviada a {$client->name} ({$days} días)");
                }
            }
        }

        $this->info('Proceso completado.');
        return 0;
    }
}