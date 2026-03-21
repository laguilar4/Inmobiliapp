<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Usuario superadmin por defecto
        User::updateOrCreate(
            ['email' => 'test@test.com'],
            [
                'name' => 'Super Admin',
                'password' => 'testeo321',
                'role' => 'superadmin',
                'email_verified_at' => now(),
            ]
        );
    }
}
