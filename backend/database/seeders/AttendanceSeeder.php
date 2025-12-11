<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Attendance;
use App\Models\Client;
use App\Models\Staff;

class AttendanceSeeder extends Seeder
{
    public function run(): void
    {
        $clients = Client::all();
        $staffMembers = Staff::all();

        foreach ($clients as $client) {
            // Crear 3 registros de asistencia por cliente
            for ($i = 0; $i < 3; $i++) {
                $checkInTime = now()->subDays($i)->setHour(6)->setMinute(0)->setSecond(0);
                $checkOutTime = $checkInTime->copy()->addHours(2);

                Attendance::create([
                    'client_id' => $client->id,
                    'staff_id' => $staffMembers->where('gym_owner_id', $client->gym_owner_id)->first()->id,
                    'check_in' => $checkInTime,
                    'check_out' => $checkOutTime,
                    'status' => 'completed',
                ]);
            }
        }
    }
}