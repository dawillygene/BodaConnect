<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\RideRequestResource;
use App\Services\AdminDashboardMetricsService;
use App\Services\RideRequestService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(
        private readonly RideRequestService $rideRequestService,
        private readonly AdminDashboardMetricsService $adminDashboardMetricsService,
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        $user = $request->user();
        $rides = $this->rideRequestService->historyForUser($user);

        $payload = match ($user->role) {
            'admin' => [
                'stats' => $this->adminDashboardMetricsService->stats(),
                'recent_rides' => RideRequestResource::collection($rides->take(5))->resolve(),
            ],
            'rider' => [
                'stats' => [
                    'assigned' => $rides->whereIn('status', ['Assigned', 'Accepted', 'In Progress'])->count(),
                    'completed' => $rides->where('status', 'Completed')->count(),
                    'total_rides' => $rides->count(),
                ],
                'assigned_trips' => RideRequestResource::collection(
                    $rides->whereIn('status', ['Assigned', 'Accepted', 'In Progress'])->values()
                )->resolve(),
                'completed_trips' => RideRequestResource::collection(
                    $rides->where('status', 'Completed')->values()
                )->resolve(),
            ],
            default => [
                'stats' => [
                    'total' => $rides->count(),
                    'pending' => $rides->where('status', 'Pending')->count(),
                    'completed' => $rides->where('status', 'Completed')->count(),
                    'cancelled' => $rides->where('status', 'Cancelled')->count(),
                ],
                'recent_rides' => RideRequestResource::collection($rides->take(5))->resolve(),
            ],
        };

        return response()->json([
            'role' => $user->role,
            'dashboard' => $payload,
        ]);
    }
}
