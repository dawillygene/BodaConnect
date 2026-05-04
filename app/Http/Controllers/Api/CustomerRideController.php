<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreRideRequestRequest;
use App\Http\Resources\RideRequestResource;
use App\Models\RideRequest;
use App\Services\RideRequestService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CustomerRideController extends Controller
{
    public function __construct(private readonly RideRequestService $rideRequestService) {}

    public function index(Request $request): JsonResponse
    {
        $rides = $this->rideRequestService
            ->historyForUser($request->user())
            ->values();

        return response()->json([
            'rides' => RideRequestResource::collection($rides)->resolve(),
        ]);
    }

    public function store(StoreRideRequestRequest $request): JsonResponse
    {
        $ride = $this->rideRequestService->createForCustomer($request->user(), $request->validated());
        $ride->load('rider:id,name,email,phone,role,status');

        return response()->json([
            'message' => 'Ride requested successfully.',
            'ride' => RideRequestResource::make($ride)->resolve(),
        ], 201);
    }

    public function show(Request $request, RideRequest $rideRequest): JsonResponse
    {
        abort_if($rideRequest->customer_id !== $request->user()->id, 403);

        $rideRequest->load(['customer:id,name,email,phone,role,status', 'rider:id,name,email,phone,role,status']);

        return response()->json([
            'ride' => RideRequestResource::make($rideRequest)->resolve(),
        ]);
    }

    public function cancel(Request $request, RideRequest $rideRequest): JsonResponse
    {
        $ride = $this->rideRequestService->cancelByCustomer($request->user(), $rideRequest);
        $ride->load(['customer:id,name,email,phone,role,status', 'rider:id,name,email,phone,role,status']);

        return response()->json([
            'message' => 'Ride cancelled successfully.',
            'ride' => RideRequestResource::make($ride)->resolve(),
        ]);
    }
}
