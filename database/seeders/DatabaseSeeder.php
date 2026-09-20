<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database with demo data (§20 of the spec).
     * All accounts, senders, recipients and shipments created here are
     * fictional and clearly identified as such (`.test`/`.example` domains).
     */
    public function run(): void
    {
        $this->call([
            SettingsSeeder::class,
            AdminUserSeeder::class,
            ShipmentSeeder::class,
        ]);
    }
}
