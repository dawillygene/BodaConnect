<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::query()->firstOrCreate(
            ['email' => 'admin@bodaconnect.test'],
            [
                'name' => 'System Admin',
                'phone' => '0700000000',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'status' => 'active',
            ]
        );

        User::factory()->count(3)->create(['role' => 'rider']);
        User::factory()->count(5)->create(['role' => 'customer']);
    }
}
