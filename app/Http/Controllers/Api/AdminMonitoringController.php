<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\MonitoringService;
use Illuminate\Http\JsonResponse;

class AdminMonitoringController extends Controller
{
    public function __construct(private readonly MonitoringService $monitoringService) {}

    public function __invoke(): JsonResponse
    {
        return response()->json([
            'monitoring' => $this->monitoringService->status(),
        ]);
    }
}
