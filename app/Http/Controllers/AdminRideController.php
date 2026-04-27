<?php

namespace App\Http\Controllers;

use App\Models\RideRequest;
use App\Models\User;
use App\Services\AdminRideService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminRideController extends Controller
{
    public function __construct(private readonly AdminRideService $adminRideService) {}

    public function dashboard(): View
    {
        $rides = RideRequest::query();

        return view('admin.dashboard', [
            'stats' => [
                'customers' => User::query()->where('role', 'customer')->count(),
                'riders' => User::query()->where('role', 'rider')->count(),
                'total_rides' => (clone $rides)->count(),
                'pending' => (clone $rides)->where('status', 'Pending')->count(),
                'completed' => (clone $rides)->where('status', 'Completed')->count(),
                'cancelled' => (clone $rides)->where('status', 'Cancelled')->count(),
            ],
        ]);
    }

    public function index(Request $request): JsonResponse|View
    {
        $rides = RideRequest::query()
            ->with(['customer:id,name,email,phone', 'rider:id,name,email,phone'])
            ->latest()
            ->get();

        if ($request->expectsJson()) {
            return response()->json($rides);
        }

        return view('admin.rides.index', [
            'rides' => $rides,
            'riders' => User::query()->where('role', 'rider')->where('status', 'active')->orderBy('name')->get(),
        ]);
    }

    public function show(Request $request, RideRequest $rideRequest): JsonResponse|View
    {
        $rideRequest->load(['customer:id,name,email,phone', 'rider:id,name,email,phone']);

        if ($request->expectsJson()) {
            return response()->json($rideRequest);
        }

        return view('admin.rides.show', ['ride' => $rideRequest]);
    }

    public function assignRider(Request $request, RideRequest $rideRequest): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'rider_id' => ['required', 'integer', 'exists:users,id'],
        ]);

        $rider = User::query()->findOrFail($validated['rider_id']);
        $updatedRide = $this->adminRideService->assignRider($rideRequest, $rider);

        if ($request->input('_response') === 'web') {
            return redirect()->route('admin.rides.index')->with('success', "Ride #{$updatedRide->id} assigned successfully.");
        }

        return response()->json($updatedRide);
    }
}
