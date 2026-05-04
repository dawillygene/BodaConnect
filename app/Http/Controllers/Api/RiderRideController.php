<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\RideRequestResource;
use App\Models\RideRequest;
use App\Services\RiderRideService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RiderRideController extends Controller
{
    public function __construct(private readonly RiderRideService $riderRideService) {}

    public function index(Request $request): JsonResponse
    {
        $rides = RideRequest::query()
            ->with(['customer:id,name,email,phone,role,status', 'rider:id,name,email,phone,role,status'])
            ->where('rider_id', $request->user()->id)
            ->latest()
            ->get();

        return response()->json([
            'rides' => RideRequestResource::collection($rides)->resolve(),
        ]);
    }

    public function accept(Request $request, RideRequest $rideRequest): JsonResponse
    {
        $ride = $this->riderRideService->acceptRide($request->user(), $rideRequest);
        $ride->load(['customer:id,name,email,phone,role,status', 'rider:id,name,email,phone,role,status']);

        return response()->json([
            'message' => "Ride #{$ride->id} accepted.",
            'ride' => RideRequestResource::make($ride)->resolve(),
        ]);
    }

    public function start(Request $request, RideRequest $rideRequest): JsonResponse
    {
        $ride = $this->riderRideService->startRide($request->user(), $rideRequest);
        $ride->load(['customer:id,name,email,phone,role,status', 'rider:id,name,email,phone,role,status']);

        return response()->json([
            'message' => "Ride #{$ride->id} started.",
            'ride' => RideRequestResource::make($ride)->resolve(),
        ]);
    }

    public function complete(Request $request, RideRequest $rideRequest): JsonResponse
    {
        $ride = $this->riderRideService->completeRide($request->user(), $rideRequest);
        $ride->load(['customer:id,name,email,phone,role,status', 'rider:id,name,email,phone,role,status']);

        return response()->json([
            'message' => "Ride #{$ride->id} completed.",
            'ride' => RideRequestResource::make($ride)->resolve(),
        ]);
    }
}
