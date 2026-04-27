<?php

namespace Tests\Feature;

use App\Models\RideRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RideLifecycleTest extends TestCase
{
    use RefreshDatabase;

    public function test_full_ride_lifecycle_from_request_to_completion(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $admin = User::factory()->create(['role' => 'admin']);
        $rider = User::factory()->create(['role' => 'rider']);

        $createResponse = $this->actingAs($customer)->post('/rides', [
            'pickup_location' => 'Kampala Road',
            'destination_location' => 'Ntinda',
            'notes' => 'Call on arrival',
        ]);

        $createResponse->assertCreated();

        $rideId = $createResponse->json('id');
        $this->assertNotNull($rideId);

        $this->actingAs($admin)->post("/admin/rides/{$rideId}/assign", [
            'rider_id' => $rider->id,
        ])->assertOk();

        $this->assertDatabaseHas('ride_requests', [
            'id' => $rideId,
            'status' => 'Assigned',
            'rider_id' => $rider->id,
        ]);

        $this->actingAs($rider)->post("/rider/rides/{$rideId}/accept")->assertOk();
        $this->actingAs($rider)->post("/rider/rides/{$rideId}/start")->assertOk();
        $this->actingAs($rider)->post("/rider/rides/{$rideId}/complete")->assertOk();

        $this->assertDatabaseHas('ride_requests', [
            'id' => $rideId,
            'status' => 'Completed',
        ]);
    }

    public function test_customer_can_only_cancel_pending_rides(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $rideRequest = RideRequest::factory()->create([
            'customer_id' => $customer->id,
            'status' => 'Assigned',
        ]);

        $this->actingAs($customer)
            ->postJson("/rides/{$rideRequest->id}/cancel")
            ->assertStatus(422);
    }
}
