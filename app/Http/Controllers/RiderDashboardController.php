<?php

namespace App\Http\Controllers;

use App\Models\RideRequest;
use App\Services\RiderRideService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RiderDashboardController extends Controller
{
    public function __construct(private readonly RiderRideService $riderRideService) {}

    public function index(Request $request): JsonResponse|View
    {
        $trips = RideRequest::query()
            ->where('rider_id', $request->user()->id)
            ->latest()
            ->get();

        if ($request->expectsJson()) {
            return response()->json($trips);
        }

        return view('rider.dashboard', [
            'assignedTrips' => $trips->whereIn('status', ['Assigned', 'Accepted', 'In Progress'])->values(),
            'completedTrips' => $trips->where('status', 'Completed')->values(),
        ]);
    }

    public function acceptRide(Request $request, RideRequest $rideRequest): JsonResponse|RedirectResponse
    {
        $updatedRide = $this->riderRideService->acceptRide($request->user(), $rideRequest);

        if ($request->input('_response') === 'web') {
            return redirect()->route('rider.dashboard')->with('success', "Ride #{$updatedRide->id} accepted.");
        }

        return response()->json($updatedRide);
    }

    public function startRide(Request $request, RideRequest $rideRequest): JsonResponse|RedirectResponse
    {
        $updatedRide = $this->riderRideService->startRide($request->user(), $rideRequest);

        if ($request->input('_response') === 'web') {
            return redirect()->route('rider.dashboard')->with('success', "Ride #{$updatedRide->id} started.");
        }

        return response()->json($updatedRide);
    }

    public function completeRide(Request $request, RideRequest $rideRequest): JsonResponse|RedirectResponse
    {
        $updatedRide = $this->riderRideService->completeRide($request->user(), $rideRequest);

        if ($request->input('_response') === 'web') {
            return redirect()->route('rider.dashboard')->with('success', "Ride #{$updatedRide->id} completed.");
        }

        return response()->json($updatedRide);
    }
}
