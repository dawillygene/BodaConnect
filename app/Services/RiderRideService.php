<?php

namespace App\Services;

use App\Events\RideStatusUpdated;
use App\Models\RideRequest;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class RiderRideService
{
    public function acceptRide(User $rider, RideRequest $rideRequest): RideRequest
    {
        return $this->transition($rider, $rideRequest, 'Assigned', 'Accepted');
    }

    public function startRide(User $rider, RideRequest $rideRequest): RideRequest
    {
        return $this->transition($rider, $rideRequest, 'Accepted', 'In Progress');
    }

    public function completeRide(User $rider, RideRequest $rideRequest): RideRequest
    {
        return $this->transition($rider, $rideRequest, 'In Progress', 'Completed');
    }

    private function transition(User $rider, RideRequest $rideRequest, string $from, string $to): RideRequest
    {
        if ($rideRequest->rider_id !== $rider->id) {
            throw ValidationException::withMessages([
                'ride' => 'This ride is not assigned to you.',
            ]);
        }

        if ($rideRequest->status !== $from) {
            throw ValidationException::withMessages([
                'status' => "Ride must be in {$from} status.",
            ]);
        }

        $rideRequest->status = $to;
        $rideRequest->save();
        $rideRequest->refresh();

        RideStatusUpdated::dispatch($rideRequest);

        return $rideRequest;
    }
}
