<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class MonitoringStatusApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_monitoring_status(): void
    {
        config()->set('services.monitoring.elasticsearch_url', 'http://elasticsearch:9200');
        config()->set('services.monitoring.kibana_url', 'http://kibana:5601');
        config()->set('services.monitoring.metricbeat_index', 'metricbeat-*');
        config()->set('services.monitoring.database_metric_module', 'mysql');

        Http::fake([
            'http://elasticsearch:9200' => Http::response([
                'cluster_name' => 'docker-cluster',
                'version' => ['number' => '8.14.3'],
            ]),
            'http://elasticsearch:9200/_cluster/health' => Http::response([
                'status' => 'green',
            ]),
            'http://elasticsearch:9200/metricbeat-*/_search' => Http::sequence()
                ->push([
                    'hits' => [
                        'total' => ['value' => 3],
                        'hits' => [
                            [
                                '_source' => [
                                    '@timestamp' => '2026-05-06T08:30:00Z',
                                    'container' => ['name' => 'bodaconnect-app'],
                                    'event' => ['module' => 'docker'],
                                    'host' => ['name' => 'docker-host'],
                                ],
                            ],
                        ],
                    ],
                ])
                ->push([
                    'hits' => [
                        'total' => ['value' => 2],
                        'hits' => [
                            [
                                '_source' => [
                                    '@timestamp' => '2026-05-06T08:31:00Z',
                                    'event' => ['module' => 'mysql'],
                                    'host' => ['name' => 'docker-host'],
                                    'service' => ['address' => 'db:3306'],
                                ],
                            ],
                        ],
                    ],
                ]),
            'http://kibana:5601/api/status' => Http::response([
                'name' => 'bodaconnect-kibana',
                'version' => ['number' => '8.14.3'],
                'status' => [
                    'overall' => ['level' => 'available'],
                ],
            ]),
        ]);

        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        Sanctum::actingAs($admin);

        $response = $this->getJson('/api/admin/monitoring');

        $response
            ->assertOk()
            ->assertJsonPath('monitoring.elasticsearch.reachable', true)
            ->assertJsonPath('monitoring.elasticsearch.cluster_name', 'docker-cluster')
            ->assertJsonPath('monitoring.kibana.reachable', true)
            ->assertJsonPath('monitoring.kibana.status', 'available')
            ->assertJsonPath('monitoring.metricbeat.reachable', true)
            ->assertJsonPath('monitoring.metricbeat.has_recent_metrics', true)
            ->assertJsonPath('monitoring.metricbeat.last_container_name', 'bodaconnect-app')
            ->assertJsonPath('monitoring.database.reachable', true)
            ->assertJsonPath('monitoring.database.metrics_reachable', true)
            ->assertJsonPath('monitoring.database.has_recent_metrics', true)
            ->assertJsonPath('monitoring.database.metrics_module', 'mysql')
            ->assertJsonPath('monitoring.database.service_address', 'db:3306');
    }

    public function test_non_admin_cannot_view_monitoring_status(): void
    {
        $customer = User::factory()->create([
            'role' => 'customer',
        ]);

        Sanctum::actingAs($customer);

        $this->getJson('/api/admin/monitoring')
            ->assertForbidden();
    }
}
