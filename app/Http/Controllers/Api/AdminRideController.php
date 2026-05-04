<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\AssignRideRequestRequest;
use App\Http\Resources\RideRequestResource;
use App\Http\Resources\UserResource;
use App\Models\RideRequest;
use App\Models\User;
use App\Services\AdminRideService;
use Illuminate\Http\JsonResponse;

class AdminRideController extends Controller
{
    public function __construct(private readonly AdminRideService $adminRideService) {}

    public function index(): JsonResponse
    {
        $rides = RideRequest::query()
            ->with(['customer:id,name,email,phone,role,status', 'rider:id,name,email,phone,role,status'])
            ->latest()
            ->get();

        $riders = User::query()
            ->where('role', 'rider')
            ->where('status', 'active')
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'phone', 'role', 'status', 'created_at', 'updated_at']);

        return response()->json([
            'rides' => RideRequestResource::collection($rides)->resolve(),
            'available_riders' => UserResource::collection($riders)->resolve(),
        ]);
    }

    public function show(RideRequest $rideRequest): JsonResponse
    {
        $rideRequest->load(['customer:id,name,email,phone,role,status', 'rider:id,name,email,phone,role,status']);

        return response()->json([
            'ride' => RideRequestResource::make($rideRequest)->resolve(),
        ]);
    }

    public function assign(AssignRideRequestRequest $request, RideRequest $rideRequest): JsonResponse
    {
        $rider = User::query()->findOrFail($request->validated()['rider_id']);
        $updatedRide = $this->adminRideService->assignRider($rideRequest, $rider);
        $updatedRide->load(['customer:id,name,email,phone,role,status', 'rider:id,name,email,phone,role,status']);

        return response()->json([
            'message' => "Ride #{$updatedRide->id} assigned successfully.",
            'ride' => RideRequestResource::make($updatedRide)->resolve(),
        ]);
    }
}
