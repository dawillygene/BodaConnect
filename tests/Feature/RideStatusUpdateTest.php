<?php

namespace Tests\Feature;

use App\Events\RideStatusUpdated;
use App\Listeners\PublishRideStatusUpdate;
use App\Models\RideRequest;
use App\Models\User;
use App\Services\RideStatusPublisher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Laravel\Sanctum\Sanctum;
use Mockery\MockInterface;
use Tests\TestCase;

class RideStatusUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_ride_status_update_event_triggers_the_mqtt_listener(): void
    {
        $rideRequest = RideRequest::factory()->create([
            'customer_id' => User::factory()->create(['role' => 'customer'])->id,
            'rider_id' => User::factory()->create(['role' => 'rider'])->id,
            'status' => 'Accepted',
        ]);

        $this->mock(RideStatusPublisher::class, function (MockInterface $mock) use ($rideRequest): void {
            $mock->shouldReceive('publish')
                ->once()
                ->with(
                    "ride/status/{$rideRequest->customer_id}",
                    \Mockery::type('array'),
                );

            $mock->shouldReceive('publish')
                ->once()
                ->with(
                    'ride/status/admin',
                    \Mockery::type('array'),
                );
        });

        RideStatusUpdated::dispatch($rideRequest);
    }

    public function test_ride_status_updates_are_dispatched_for_rider_transitions(): void
    {
        $rider = User::factory()->create(['role' => 'rider']);
        $rideRequest = RideRequest::factory()->create([
            'customer_id' => User::factory()->create(['role' => 'customer'])->id,
            'rider_id' => $rider->id,
            'status' => 'Assigned',
        ]);

        Sanctum::actingAs($rider);
        Event::fake([RideStatusUpdated::class]);

        $this->postJson("/api/rider/rides/{$rideRequest->id}/accept")->assertOk();
        $this->postJson("/api/rider/rides/{$rideRequest->id}/start")->assertOk();
        $this->postJson("/api/rider/rides/{$rideRequest->id}/complete")->assertOk();

        Event::assertDispatched(RideStatusUpdated::class, 3);
        Event::assertDispatched(RideStatusUpdated::class, function (RideStatusUpdated $event): bool {
            return $event->rideRequest->status === 'Accepted';
        });
        Event::assertDispatched(RideStatusUpdated::class, function (RideStatusUpdated $event): bool {
            return $event->rideRequest->status === 'In Progress';
        });
        Event::assertDispatched(RideStatusUpdated::class, function (RideStatusUpdated $event): bool {
            return $event->rideRequest->status === 'Completed';
        });
    }

    public function test_ride_status_update_is_dispatched_when_admin_assigns_a_rider(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $rider = User::factory()->create(['role' => 'rider', 'status' => 'active']);
        $rideRequest = RideRequest::factory()->create([
            'customer_id' => User::factory()->create(['role' => 'customer'])->id,
            'status' => 'Pending',
        ]);

        Sanctum::actingAs($admin);
        Event::fake([RideStatusUpdated::class]);

        $this->postJson("/api/admin/rides/{$rideRequest->id}/assign", [
            'rider_id' => $rider->id,
        ])->assertOk();

        Event::assertDispatched(RideStatusUpdated::class, function (RideStatusUpdated $event) use ($rideRequest, $rider): bool {
            return $event->rideRequest->is($rideRequest)
                && $event->rideRequest->status === 'Assigned'
                && $event->rideRequest->rider_id === $rider->id;
        });
    }

    public function test_ride_status_update_is_dispatched_when_customer_creates_a_ride(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);

        Sanctum::actingAs($customer);
        Event::fake([RideStatusUpdated::class]);

        $this->postJson('/api/rides', [
            'pickup_location' => 'Kihonda',
            'destination_location' => 'Same',
            'notes' => 'New ride',
        ])->assertCreated();

        Event::assertDispatched(RideStatusUpdated::class, function (RideStatusUpdated $event) use ($customer): bool {
            return $event->rideRequest->customer_id === $customer->id
                && $event->rideRequest->status === 'Pending';
        });
    }

    public function test_listener_publishes_expected_topic_and_payload(): void
    {
        $rideRequest = RideRequest::factory()->create([
            'customer_id' => User::factory()->create(['role' => 'customer'])->id,
            'rider_id' => User::factory()->create(['role' => 'rider'])->id,
            'status' => 'Accepted',
        ]);

        $this->mock(RideStatusPublisher::class, function (MockInterface $mock) use ($rideRequest): void {
            $mock->shouldReceive('publish')
                ->once()
                ->with(
                    "ride/status/{$rideRequest->customer_id}",
                    \Mockery::on(function (array $payload) use ($rideRequest): bool {
                        return $payload['ride_id'] === $rideRequest->id
                            && $payload['customer_id'] === $rideRequest->customer_id
                            && $payload['rider_id'] === $rideRequest->rider_id
                            && $payload['rider']['id'] === $rideRequest->rider_id
                            && $payload['rider']['name'] === $rideRequest->rider->name
                            && $payload['status'] === 'Accepted'
                            && $payload['pickup_location'] === $rideRequest->pickup_location
                            && $payload['destination_location'] === $rideRequest->destination_location
                            && $payload['updated_at'] === $rideRequest->updated_at?->toISOString();
                    }),
                );

            $mock->shouldReceive('publish')
                ->once()
                ->with(
                    'ride/status/admin',
                    \Mockery::on(function (array $payload) use ($rideRequest): bool {
                        return $payload['ride_id'] === $rideRequest->id
                            && $payload['customer_id'] === $rideRequest->customer_id
                            && $payload['rider_id'] === $rideRequest->rider_id
                            && $payload['rider']['id'] === $rideRequest->rider_id
                            && $payload['rider']['name'] === $rideRequest->rider->name
                            && $payload['status'] === 'Accepted'
                            && $payload['pickup_location'] === $rideRequest->pickup_location
                            && $payload['destination_location'] === $rideRequest->destination_location
                            && $payload['updated_at'] === $rideRequest->updated_at?->toISOString();
                    }),
                );
        });

        $listener = $this->app->make(PublishRideStatusUpdate::class);
        $listener->handle(new RideStatusUpdated($rideRequest));
    }
}
