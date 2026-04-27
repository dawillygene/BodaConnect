<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserManagementService
{
    public function customers(): LengthAwarePaginator
    {
        return User::query()->where('role', 'customer')->latest()->paginate(15);
    }

    public function riders(): LengthAwarePaginator
    {
        return User::query()->where('role', 'rider')->latest()->paginate(15);
    }

    public function createRider(array $data): User
    {
        return User::query()->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'password' => Hash::make($data['password']),
            'role' => 'rider',
            'status' => $data['status'] ?? 'active',
        ]);
    }

    public function updateRider(User $rider, array $data): User
    {
        if ($rider->role !== 'rider') {
            throw ValidationException::withMessages([
                'user' => 'Selected user is not a rider.',
            ]);
        }

        $rider->fill([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'status' => $data['status'],
        ]);

        if (! empty($data['password'])) {
            $rider->password = Hash::make($data['password']);
        }

        $rider->save();

        return $rider;
    }

    public function deactivateUser(User $user): User
    {
        if ($user->role === 'admin') {
            throw ValidationException::withMessages([
                'user' => 'Admin users cannot be deactivated.',
            ]);
        }

        $user->status = 'inactive';
        $user->save();

        return $user;
    }
}
