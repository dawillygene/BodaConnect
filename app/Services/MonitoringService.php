<?php

namespace App\Services;

use Illuminate\Database\QueryException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use PDO;
use Throwable;

class MonitoringService
{
    /**
     * Retrieve monitoring stack status from the Docker-backed Elastic services.
     *
     * @return array{
     *     elasticsearch: array<string, mixed>,
     *     kibana: array<string, mixed>,
     *     metricbeat: array<string, mixed>,
     *     database: array<string, mixed>
     * }
     */
    public function status(): array
    {
        $elasticsearchUrl = $this->normalizeUrl((string) config('services.monitoring.elasticsearch_url'));
        $kibanaUrl = $this->normalizeUrl((string) config('services.monitoring.kibana_url'));
        $metricbeatIndex = (string) config('services.monitoring.metricbeat_index');
        $applicationMetricsIndex = (string) config('services.monitoring.application_metrics_index', 'bodaconnect-admin-metrics');
        $databaseMetricModule = (string) config('services.monitoring.database_metric_module', 'mysql');

        return [
            'elasticsearch' => $this->elasticsearchStatus($elasticsearchUrl),
            'kibana' => $this->kibanaStatus($kibanaUrl),
            'metricbeat' => $this->metricbeatStatus($elasticsearchUrl, $metricbeatIndex),
            'database' => $this->databaseStatus($elasticsearchUrl, $metricbeatIndex, $databaseMetricModule),
            'application_metrics' => $this->applicationMetricsStatus($elasticsearchUrl, $applicationMetricsIndex),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function elasticsearchStatus(string $baseUrl): array
    {
        try {
            $infoResponse = $this->httpClient()->get($baseUrl);
            $healthResponse = $this->httpClient()->get("{$baseUrl}/_cluster/health");

            return [
                'url' => $baseUrl,
                'reachable' => $infoResponse->successful() && $healthResponse->successful(),
                'cluster_name' => $infoResponse->json('cluster_name'),
                'version' => $infoResponse->json('version.number'),
                'status' => $healthResponse->json('status'),
            ];
        } catch (ConnectionException $exception) {
            return $this->unreachableService($baseUrl, $exception->getMessage());
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function kibanaStatus(string $baseUrl): array
    {
        try {
            $response = $this->httpClient()
                ->withHeaders(['kbn-xsrf' => 'monitoring-status'])
                ->get("{$baseUrl}/api/status");

            return [
                'url' => $baseUrl,
                'reachable' => $response->successful(),
                'name' => $response->json('name'),
                'version' => $response->json('version.number'),
                'status' => $response->json('status.overall.level'),
            ];
        } catch (ConnectionException $exception) {
            return $this->unreachableService($baseUrl, $exception->getMessage());
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function metricbeatStatus(string $elasticsearchUrl, string $metricbeatIndex): array
    {
        try {
            $response = $this->httpClient()->post("{$elasticsearchUrl}/{$metricbeatIndex}/_search", [
                'size' => 1,
                'sort' => [
                    ['@timestamp' => ['order' => 'desc']],
                ],
                'track_total_hits' => true,
                '_source' => ['@timestamp', 'container.name', 'event.module', 'host.name'],
                'query' => [
                    'bool' => [
                        'filter' => [
                            ['range' => ['@timestamp' => ['gte' => 'now-15m']]],
                        ],
                    ],
                ],
            ]);

            $latestDocument = $response->json('hits.hits.0._source', []);

            return [
                'index' => $metricbeatIndex,
                'reachable' => $response->successful(),
                'has_recent_metrics' => (int) $response->json('hits.total.value', 0) > 0,
                'recent_documents' => (int) $response->json('hits.total.value', 0),
                'last_event_at' => $latestDocument['@timestamp'] ?? null,
                'last_container_name' => $latestDocument['container']['name'] ?? null,
                'module' => $latestDocument['event']['module'] ?? null,
                'host' => $latestDocument['host']['name'] ?? null,
            ];
        } catch (ConnectionException $exception) {
            return [
                'index' => $metricbeatIndex,
                'reachable' => false,
                'has_recent_metrics' => false,
                'recent_documents' => 0,
                'last_event_at' => null,
                'error' => $exception->getMessage(),
            ];
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function databaseStatus(string $elasticsearchUrl, string $metricbeatIndex, string $databaseMetricModule): array
    {
        $connection = DB::connection();
        $databaseConfig = $connection->getConfig();

        return array_merge(
            [
                'connection' => $connection->getName(),
                'driver' => $connection->getDriverName(),
                'host' => $databaseConfig['host'] ?? null,
                'port' => $databaseConfig['port'] ?? null,
                'database' => $databaseConfig['database'] ?? null,
            ],
            $this->databaseConnectionStatus(),
            $this->databaseMetricsStatus($elasticsearchUrl, $metricbeatIndex, $databaseMetricModule)
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function applicationMetricsStatus(string $elasticsearchUrl, string $applicationMetricsIndex): array
    {
        try {
            $response = $this->httpClient()->post("{$elasticsearchUrl}/{$applicationMetricsIndex}/_search", [
                'size' => 1,
                'sort' => [
                    ['@timestamp' => ['order' => 'desc']],
                ],
                'track_total_hits' => true,
                '_source' => [
                    '@timestamp',
                    'app.environment',
                    'app.name',
                    'metrics.total_users',
                    'metrics.customers',
                    'metrics.riders',
                    'metrics.total_rides',
                    'metrics.pending',
                    'metrics.completed',
                    'metrics.cancelled',
                ],
                'query' => [
                    'bool' => [
                        'filter' => [
                            ['range' => ['@timestamp' => ['gte' => 'now-24h']]],
                        ],
                    ],
                ],
            ]);

            $latestDocument = $response->json('hits.hits.0._source', []);

            return [
                'index' => $applicationMetricsIndex,
                'reachable' => $response->successful(),
                'has_recent_metrics' => (int) $response->json('hits.total.value', 0) > 0,
                'recent_documents' => (int) $response->json('hits.total.value', 0),
                'last_event_at' => $latestDocument['@timestamp'] ?? null,
                'application_name' => $latestDocument['app']['name'] ?? null,
                'application_environment' => $latestDocument['app']['environment'] ?? null,
                'latest_snapshot' => $latestDocument['metrics'] ?? null,
            ];
        } catch (ConnectionException $exception) {
            return [
                'index' => $applicationMetricsIndex,
                'reachable' => false,
                'has_recent_metrics' => false,
                'recent_documents' => 0,
                'last_event_at' => null,
                'application_name' => null,
                'application_environment' => null,
                'latest_snapshot' => null,
                'error' => $exception->getMessage(),
            ];
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function databaseConnectionStatus(): array
    {
        try {
            $pdo = DB::connection()->getPdo();

            return [
                'reachable' => true,
                'server_version' => $pdo->getAttribute(PDO::ATTR_SERVER_VERSION) ?: null,
            ];
        } catch (QueryException|Throwable $exception) {
            return [
                'reachable' => false,
                'server_version' => null,
                'connection_error' => $exception->getMessage(),
            ];
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function databaseMetricsStatus(string $elasticsearchUrl, string $metricbeatIndex, string $databaseMetricModule): array
    {
        try {
            $response = $this->httpClient()->post("{$elasticsearchUrl}/{$metricbeatIndex}/_search", [
                'size' => 1,
                'sort' => [
                    ['@timestamp' => ['order' => 'desc']],
                ],
                'track_total_hits' => true,
                '_source' => ['@timestamp', 'event.module', 'host.name', 'service.address'],
                'query' => [
                    'bool' => [
                        'filter' => [
                            ['term' => ['event.module' => $databaseMetricModule]],
                            ['range' => ['@timestamp' => ['gte' => 'now-15m']]],
                        ],
                    ],
                ],
            ]);

            $latestDocument = $response->json('hits.hits.0._source', []);

            return [
                'metrics_reachable' => $response->successful(),
                'metrics_module' => $databaseMetricModule,
                'has_recent_metrics' => (int) $response->json('hits.total.value', 0) > 0,
                'recent_metric_documents' => (int) $response->json('hits.total.value', 0),
                'last_metric_at' => $latestDocument['@timestamp'] ?? null,
                'last_metric_host' => $latestDocument['host']['name'] ?? null,
                'service_address' => $latestDocument['service']['address'] ?? null,
            ];
        } catch (ConnectionException $exception) {
            return [
                'metrics_reachable' => false,
                'metrics_module' => $databaseMetricModule,
                'has_recent_metrics' => false,
                'recent_metric_documents' => 0,
                'last_metric_at' => null,
                'service_address' => null,
                'metrics_error' => $exception->getMessage(),
            ];
        }
    }

    private function normalizeUrl(string $url): string
    {
        return rtrim($url, '/');
    }

    /**
     * @return array<string, mixed>
     */
    private function unreachableService(string $baseUrl, string $error): array
    {
        return [
            'url' => $baseUrl,
            'reachable' => false,
            'error' => $error,
        ];
    }

    private function httpClient()
    {
        return Http::acceptJson()
            ->connectTimeout(2)
            ->timeout(5)
            ->retry([100, 200], throw: false);
    }
}
