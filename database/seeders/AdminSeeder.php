<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Create the first administrator without touching existing data, so it is
     * safe to run on a database that already has users:
     *
     *   php artisan db:seed --class=AdminSeeder
     *
     * Credentials come from ADMIN_EMAIL / ADMIN_PASSWORD / ADMIN_NAME in .env
     * (defaults: admin@example.com / password). An existing account keeps its
     * password and is only promoted to admin.
     */
    public function run(): void
    {
        $email = env('ADMIN_EMAIL', 'admin@example.com');

        $admin = User::firstOrCreate(
            ['email' => $email],
            ['name' => env('ADMIN_NAME', 'Admin'), 'password' => env('ADMIN_PASSWORD', 'password')],
        );

        $admin->forceFill(['role' => User::ROLE_ADMIN, 'email_verified_at' => $admin->email_verified_at ?? now()])->save();

        $admin->profile()->firstOrCreate([], ['timezone' => 'Asia/Makassar', 'onboarded_at' => now()]);

        $this->command?->info("Admin ready: {$admin->email}");
    }
}
