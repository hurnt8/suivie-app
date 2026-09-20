<?php

namespace Database\Seeders;

use App\Models\Settings;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        Settings::query()->updateOrCreate(['id' => 1], [
            'company_name' => 'Livrion',
            'address' => '12 Avenue des Logistiques, 75012 Paris, France',
            'phone' => '+33 1 23 45 67 89',
            'email' => 'contact@livrion.example',
            'domain' => 'livrion.example',
            'tracking_prefix' => 'LVR',
            'currency' => 'EUR',
            'default_locale' => 'fr',
            'mail_from_address' => 'no-reply@livrion.example',
            'mail_from_name' => 'Livrion',
        ]);
    }
}
