<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            SuperUserSeeder::class,
            GymOwnerSeeder::class,
            StaffSeeder::class,
            MembershipSeeder::class,
            ClientSeeder::class,
            AttendanceSeeder::class,
            PaymentSeeder::class,
        ]);
    }
}