<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create demo users
        User::factory()->create([
            'name' => 'Super Administrador',
            'username' => 'superadmin',
            'email' => 'super@admin.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        User::factory()->create([
            'name' => 'Administrador',
            'username' => 'admin',
            'email' => 'admin@admin.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        User::factory()->create([
            'name' => 'Usuario Demo',
            'username' => 'demo',
            'email' => 'demo@demo.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
    }
}