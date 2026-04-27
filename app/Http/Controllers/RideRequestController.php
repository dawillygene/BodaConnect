<?php

namespace App\Http\Controllers;

use App\Models\RideRequest;
use App\Services\RideRequestService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RideRequestController extends Controller
{
    public function __construct(private readonly RideRequestService $rideRequestService) {}

    public function dashboard(Request $request): View
    {
        $user = $request->user();
        $rides = $this->rideRequestService->historyForUser($user);

        return view('customer.dashboard', [
            'stats' => [
                'total' => $rides->count(),
                'pending' => $rides->where('status', 'Pending')->count(),
                'completed' => $rides->where('status', 'Completed')->count(),
                'cancelled' => $rides->where('status', 'Cancelled')->count(),
            ],
            'recentRides' => $rides->take(5),
        ]);
    }

    public function index(Request $request): JsonResponse|View
    {
        $rides = $this->rideRequestService
            ->historyForUser($request->user())
            ->values();

        if ($request->expectsJson()) {
            return response()->json($rides);
        }

        return view('customer.rides.index', ['rides' => $rides]);
    }

    public function create(Request $request): JsonResponse|View
    {
        if ($request->expectsJson()) {
            return response()->json([
                'fields' => ['pickup_location', 'destination_location', 'notes'],
                'default_status' => 'Pending',
            ]);
        }

        return view('customer.rides.create');
    }

    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'pickup_location' => ['required', 'string', 'max:255'],
            'destination_location' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $ride = $this->rideRequestService->createForCustomer($request->user(), $validated);

        if ($this->isWebFormRequest($request)) {
            return redirect()->route('rides.index')->with('success', 'Ride requested successfully.');
        }

        return response()->json($ride, 201);
    }

    public function show(Request $request, RideRequest $rideRequest): JsonResponse|View
    {
        $user = $request->user();

        if ($user->role === 'customer' && $rideRequest->customer_id !== $user->id) {
            abort(403);
        }

        if ($user->role === 'rider' && $rideRequest->rider_id !== $user->id) {
            abort(403);
        }

        $rideRequest->load(['customer:id,name,phone', 'rider:id,name,phone']);

        if ($request->expectsJson()) {
            return response()->json($rideRequest);
        }

        return view('customer.rides.show', ['ride' => $rideRequest]);
    }

    public function cancel(Request $request, RideRequest $rideRequest): JsonResponse|RedirectResponse
    {
        $ride = $this->rideRequestService->cancelByCustomer($request->user(), $rideRequest);

        if ($this->isWebFormRequest($request)) {
            return redirect()->route('rides.index')->with('success', 'Ride cancelled successfully.');
        }

        return response()->json($ride);
    }

    private function isWebFormRequest(Request $request): bool
    {
        return $request->input('_response') === 'web';
    }
}
