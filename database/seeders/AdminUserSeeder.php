<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\Console\Output\ConsoleOutput;

/**
 * Demo administrator account. Credentials are intentionally printed to the
 * console rather than hard-documented, and use a `.test` domain to make
 * clear this is seed data, not a real account.
 */
class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $password = 'password';

        User::query()->updateOrCreate(
            ['email' => 'admin@livrion.test'],
            [
                'name' => 'Admin Livrion',
                'password' => Hash::make($password),
                'role' => UserRole::Admin,
                'email_verified_at' => now(),
            ],
        );

        User::query()->updateOrCreate(
            ['email' => 'operateur@livrion.test'],
            [
                'name' => 'Opérateur Livrion',
                'password' => Hash::make($password),
                'role' => UserRole::Operator,
                'email_verified_at' => now(),
            ],
        );

        (new ConsoleOutput)->writeln([
            '',
            '<info>Comptes de démonstration créés :</info>',
            '  Admin      : admin@livrion.test / '.$password,
            '  Opérateur  : operateur@livrion.test / '.$password,
            '',
        ]);
    }
}
