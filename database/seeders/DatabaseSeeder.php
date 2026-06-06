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
        User::query()->updateOrCreate(
            ['email' => 'admin@bodaconnect.test'],
            [
                'name' => 'System Admin',
                'phone' => '0700000000',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'status' => 'active',
            ]
        );

        foreach ($this->riders() as $rider) {
            User::query()->updateOrCreate(
                ['email' => $rider['email']],
                $rider,
            );
        }

        foreach ($this->customers() as $customer) {
            User::query()->updateOrCreate(
                ['email' => $customer['email']],
                $customer,
            );
        }
    }

    /**
     * @return array<int, array{name: string, email: string, phone: string, password: string, role: string, status: string}>
     */
    private function riders(): array
    {
        return [
            [
                'name' => 'Rider One',
                'email' => 'rider1@bodaconnect.test',
                'phone' => '0700000001',
                'password' => Hash::make('password'),
                'role' => 'rider',
                'status' => 'active',
            ],
            [
                'name' => 'Rider Two',
                'email' => 'rider2@bodaconnect.test',
                'phone' => '0700000002',
                'password' => Hash::make('password'),
                'role' => 'rider',
                'status' => 'active',
            ],
            [
                'name' => 'Rider Three',
                'email' => 'rider3@bodaconnect.test',
                'phone' => '0700000003',
                'password' => Hash::make('password'),
                'role' => 'rider',
                'status' => 'active',
            ],
        ];
    }

    /**
     * @return array<int, array{name: string, email: string, phone: string, password: string, role: string, status: string}>
     */
    private function customers(): array
    {
        return [
            [
                'name' => 'Customer One',
                'email' => 'customer1@bodaconnect.test',
                'phone' => '0700000011',
                'password' => Hash::make('password'),
                'role' => 'customer',
                'status' => 'active',
            ],
            [
                'name' => 'Customer Two',
                'email' => 'customer2@bodaconnect.test',
                'phone' => '0700000012',
                'password' => Hash::make('password'),
                'role' => 'customer',
                'status' => 'active',
            ],
            [
                'name' => 'Customer Three',
                'email' => 'customer3@bodaconnect.test',
                'phone' => '0700000013',
                'password' => Hash::make('password'),
                'role' => 'customer',
                'status' => 'active',
            ],
            [
                'name' => 'Customer Four',
                'email' => 'customer4@bodaconnect.test',
                'phone' => '0700000014',
                'password' => Hash::make('password'),
                'role' => 'customer',
                'status' => 'active',
            ],
            [
                'name' => 'Customer Five',
                'email' => 'customer5@bodaconnect.test',
                'phone' => '0700000015',
                'password' => Hash::make('password'),
                'role' => 'customer',
                'status' => 'active',
            ],
        ];
    }
}
