<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Payment;
use App\Models\Client;
use App\Models\Staff;
use App\Models\GymOwner;

class PaymentSeeder extends Seeder
{
    public function run(): void
    {
        $gymOwners = GymOwner::all();

        foreach ($gymOwners as $gymOwner) {
            $clients = $gymOwner->clients;
            $staff = $gymOwner->staff->first();

            foreach ($clients as $client) {
                // Pago Mensual
                Payment::create([
                    'gym_owner_id' => $gymOwner->id,
                    'client_id' => $client->id,
                    'staff_id' => $staff->id,
                    'amount' => $client->membership->price,
                    'method' => 'cash',
                    'frequency' => 'monthly',
                    'payment_date' => now()->subDays(5),
                    'note' => 'Pago de membresía mensual',
                ]);

                // Pago Diario (si tiene membresía con daily_payment activo)
                if ($client->membership->daily_payment) {
                    for ($i = 0; $i < 3; $i++) {
                        Payment::create([
                            'gym_owner_id' => $gymOwner->id,
                            'client_id' => $client->id,
                            'staff_id' => $staff->id,
                            'amount' => $client->membership->price,
                            'method' => 'card',
                            'frequency' => 'daily',
                            'payment_date' => now()->subDays($i),
                            'note' => 'Pago diario',
                        ]);
                    }
                }
            }
        }
    }
}