<?php

namespace App\Services;

use App\Models\RideRequest;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class RideRequestService
{
    public function createForCustomer(User $customer, array $data): RideRequest
    {
        return RideRequest::query()->create([
            'customer_id' => $customer->id,
            'pickup_location' => $data['pickup_location'],
            'destination_location' => $data['destination_location'],
            'notes' => $data['notes'] ?? null,
            'status' => 'Pending',
        ]);
    }

    public function cancelByCustomer(User $customer, RideRequest $rideRequest): RideRequest
    {
        if ($rideRequest->customer_id !== $customer->id) {
            throw ValidationException::withMessages([
                'ride' => 'You can only cancel your own ride requests.',
            ]);
        }

        if ($rideRequest->status !== 'Pending') {
            throw ValidationException::withMessages([
                'status' => 'Only pending rides can be cancelled.',
            ]);
        }

        $rideRequest->status = 'Cancelled';
        $rideRequest->save();

        return $rideRequest;
    }

    public function historyForUser(User $user): Collection
    {
        return match ($user->role) {
            'admin' => RideRequest::query()->with(['customer:id,name,phone', 'rider:id,name,phone'])->latest()->get(),
            'rider' => RideRequest::query()->with(['customer:id,name,phone'])->where('rider_id', $user->id)->latest()->get(),
            default => RideRequest::query()->with(['rider:id,name,phone'])->where('customer_id', $user->id)->latest()->get(),
        };
    }
}
