<?php

namespace App\Services;

use App\Models\RideRequest;
use App\Models\User;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

class AdminDashboardMetricsService
{
    /**
     * @return array{
     *     total_users: int,
     *     admins: int,
     *     customers: int,
     *     riders: int,
     *     active_riders: int,
     *     inactive_riders: int,
     *     total_rides: int,
     *     pending: int,
     *     assigned: int,
     *     accepted: int,
     *     in_progress: int,
     *     completed: int,
     *     cancelled: int
     * }
     */
    public function stats(): array
    {
        $userCounts = User::query()
            ->selectRaw('COUNT(*) as total_users')
            ->selectRaw("SUM(CASE WHEN role = 'admin' THEN 1 ELSE 0 END) as admins")
            ->selectRaw("SUM(CASE WHEN role = 'customer' THEN 1 ELSE 0 END) as customers")
            ->selectRaw("SUM(CASE WHEN role = 'rider' THEN 1 ELSE 0 END) as riders")
            ->selectRaw("SUM(CASE WHEN role = 'rider' AND status = 'active' THEN 1 ELSE 0 END) as active_riders")
            ->selectRaw("SUM(CASE WHEN role = 'rider' AND status <> 'active' THEN 1 ELSE 0 END) as inactive_riders")
            ->first();

        $rideCounts = RideRequest::query()
            ->selectRaw('COUNT(*) as total_rides')
            ->selectRaw("SUM(CASE WHEN status = 'Pending' THEN 1 ELSE 0 END) as pending")
            ->selectRaw("SUM(CASE WHEN status = 'Assigned' THEN 1 ELSE 0 END) as assigned")
            ->selectRaw("SUM(CASE WHEN status = 'Accepted' THEN 1 ELSE 0 END) as accepted")
            ->selectRaw("SUM(CASE WHEN status = 'In Progress' THEN 1 ELSE 0 END) as in_progress")
            ->selectRaw("SUM(CASE WHEN status = 'Completed' THEN 1 ELSE 0 END) as completed")
            ->selectRaw("SUM(CASE WHEN status = 'Cancelled' THEN 1 ELSE 0 END) as cancelled")
            ->first();

        return [
            'total_users' => (int) ($userCounts?->total_users ?? 0),
            'admins' => (int) ($userCounts?->admins ?? 0),
            'customers' => (int) ($userCounts?->customers ?? 0),
            'riders' => (int) ($userCounts?->riders ?? 0),
            'active_riders' => (int) ($userCounts?->active_riders ?? 0),
            'inactive_riders' => (int) ($userCounts?->inactive_riders ?? 0),
            'total_rides' => (int) ($rideCounts?->total_rides ?? 0),
            'pending' => (int) ($rideCounts?->pending ?? 0),
            'assigned' => (int) ($rideCounts?->assigned ?? 0),
            'accepted' => (int) ($rideCounts?->accepted ?? 0),
            'in_progress' => (int) ($rideCounts?->in_progress ?? 0),
            'completed' => (int) ($rideCounts?->completed ?? 0),
            'cancelled' => (int) ($rideCounts?->cancelled ?? 0),
        ];
    }

    /**
     * @return array{
     *
     *     @timestamp: string,
     *     app: array{environment: string, name: string},
     *     metrics: array<string, int>
     * }
     */
    public function snapshot(): array
    {
        return [
            '@timestamp' => now()->toIso8601String(),
            'app' => [
                'environment' => app()->environment(),
                'name' => (string) config('app.name'),
            ],
            'metrics' => $this->stats(),
        ];
    }

    /**
     * @return array{success: bool, index: string, timestamp: string, status_code: int|null, error: string|null}
     */
    public function indexSnapshot(): array
    {
        $elasticsearchUrl = rtrim((string) config('services.monitoring.elasticsearch_url'), '/');
        $metricsIndex = (string) config('services.monitoring.application_metrics_index', 'bodaconnect-admin-metrics');
        $payload = $this->snapshot();

        try {
            $response = Http::acceptJson()
                ->asJson()
                ->connectTimeout(2)
                ->timeout(5)
                ->retry([100, 200], throw: false)
                ->post("{$elasticsearchUrl}/{$metricsIndex}/_doc", $payload);

            return [
                'success' => $response->successful(),
                'index' => $metricsIndex,
                'timestamp' => $payload['@timestamp'],
                'status_code' => $response->status(),
                'error' => $response->successful() ? null : $response->body(),
            ];
        } catch (ConnectionException $exception) {
            return [
                'success' => false,
                'index' => $metricsIndex,
                'timestamp' => $payload['@timestamp'],
                'status_code' => null,
                'error' => $exception->getMessage(),
            ];
        }
    }
}
