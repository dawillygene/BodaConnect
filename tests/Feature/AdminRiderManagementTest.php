<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

//testing class


class AdminRiderManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_reactivate_an_inactive_rider(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);
        $rider = User::factory()->create([
            'role' => 'rider',
            'status' => 'inactive',
        ]);

        Sanctum::actingAs($admin);

        $response = $this->putJson("/api/admin/riders/{$rider->id}", [
            'name' => $rider->name,
            'email' => $rider->email,
            'phone' => $rider->phone,
            'status' => 'active',
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('rider.status', 'active');

        $this->assertDatabaseHas('users', [
            'id' => $rider->id,
            'status' => 'active',
        ]);
    }
}
