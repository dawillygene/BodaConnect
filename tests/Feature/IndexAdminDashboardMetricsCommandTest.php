<?php

namespace Tests\Feature;

use App\Models\RideRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class IndexAdminDashboardMetricsCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_command_indexes_admin_dashboard_metrics_into_elasticsearch(): void
    {
        config()->set('services.monitoring.elasticsearch_url', 'http://elasticsearch:9200');
        config()->set('services.monitoring.application_metrics_index', 'bodaconnect-admin-metrics');

        User::factory()->create(['role' => 'admin']);
        $customers = User::factory()->count(2)->create(['role' => 'customer']);
        $activeRider = User::factory()->create(['role' => 'rider', 'status' => 'active']);
        User::factory()->create(['role' => 'rider', 'status' => 'inactive']);

        RideRequest::factory()->create([
            'customer_id' => $customers[0]->id,
            'status' => 'Pending',
        ]);
        RideRequest::factory()->create([
            'customer_id' => $customers[0]->id,
            'rider_id' => $activeRider->id,
            'status' => 'Assigned',
        ]);
        RideRequest::factory()->create([
            'customer_id' => $customers[0]->id,
            'rider_id' => $activeRider->id,
            'status' => 'Accepted',
        ]);
        RideRequest::factory()->create([
            'customer_id' => $customers[1]->id,
            'rider_id' => $activeRider->id,
            'status' => 'In Progress',
        ]);
        RideRequest::factory()->create([
            'customer_id' => $customers[1]->id,
            'rider_id' => $activeRider->id,
            'status' => 'Completed',
        ]);
        RideRequest::factory()->create([
            'customer_id' => $customers[1]->id,
            'status' => 'Cancelled',
        ]);

        $capturedRequest = null;

        Http::fake(function ($request) use (&$capturedRequest) {
            $capturedRequest = $request;

            return Http::response([
                'result' => 'created',
            ], 201);
        });

        $this->artisan('app:index-admin-dashboard-metrics')
            ->expectsOutputToContain('Indexed admin dashboard metrics')
            ->assertSuccessful();

        $this->assertNotNull($capturedRequest);

        $data = json_decode($capturedRequest->body(), true);

        $this->assertSame('http://elasticsearch:9200/bodaconnect-admin-metrics/_doc', $capturedRequest->url());
        $this->assertSame(config('app.name'), $data['app']['name']);
        $this->assertSame(5, $data['metrics']['total_users']);
        $this->assertSame(1, $data['metrics']['admins']);
        $this->assertSame(2, $data['metrics']['customers']);
        $this->assertSame(2, $data['metrics']['riders']);
        $this->assertSame(1, $data['metrics']['active_riders']);
        $this->assertSame(1, $data['metrics']['inactive_riders']);
        $this->assertSame(6, $data['metrics']['total_rides']);
        $this->assertSame(1, $data['metrics']['pending']);
        $this->assertSame(1, $data['metrics']['assigned']);
        $this->assertSame(1, $data['metrics']['accepted']);
        $this->assertSame(1, $data['metrics']['in_progress']);
        $this->assertSame(1, $data['metrics']['completed']);
        $this->assertSame(1, $data['metrics']['cancelled']);
        $this->assertIsString($data['@timestamp']);
    }

    public function test_command_fails_when_elasticsearch_indexing_fails(): void
    {
        config()->set('services.monitoring.elasticsearch_url', 'http://elasticsearch:9200');
        config()->set('services.monitoring.application_metrics_index', 'bodaconnect-admin-metrics');

        Http::fake([
            'http://elasticsearch:9200/bodaconnect-admin-metrics/_doc' => Http::response([
                'error' => 'Elasticsearch unavailable',
            ], 503),
        ]);

        $this->artisan('app:index-admin-dashboard-metrics')
            ->expectsOutputToContain('Unable to index admin dashboard metrics')
            ->assertFailed();
    }
}
