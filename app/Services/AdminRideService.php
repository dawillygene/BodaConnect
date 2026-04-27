<?php

namespace App\Services;

use App\Models\RideRequest;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class AdminRideService
{
    public function assignRider(RideRequest $rideRequest, User $rider): RideRequest
    {
        if ($rider->role !== 'rider') {
            throw ValidationException::withMessages([
                'rider_id' => 'Selected user is not a rider.',
            ]);
        }

        if ($rider->status !== 'active') {
            throw ValidationException::withMessages([
                'rider_id' => 'Selected rider is inactive.',
            ]);
        }

        if ($rideRequest->status !== 'Pending') {
            throw ValidationException::withMessages([
                'status' => 'Only pending rides can be assigned.',
            ]);
        }

        $rideRequest->rider_id = $rider->id;
        $rideRequest->status = 'Assigned';
        $rideRequest->save();

        return $rideRequest;
    }
}
