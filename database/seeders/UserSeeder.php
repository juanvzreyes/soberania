<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name'              => 'Administrador',
            'email'             => 'admin@gmail.com',
            'password'          => '12345678',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name'              => 'Productor',
            'email'             => 'producer@gmail.com',
            'password'          => '12345678',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name'              => 'Cooperativa',
            'email'             => 'cooperative@gmail.com',
            'password'          => '12345678',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name'              => 'Consumidor',
            'email'             => 'consumer@gmail.com',
            'password'          => '12345678',
            'email_verified_at' => now(),
        ]);
    }
}
