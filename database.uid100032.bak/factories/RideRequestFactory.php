<?php

namespace Database\Factories;

use App\Models\RideRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RideRequest>
 */
class RideRequestFactory extends Factory
{
    protected $model = RideRequest::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'customer_id' => User::factory(),
            'rider_id' => null,
            'pickup_location' => fake()->streetAddress(),
            'destination_location' => fake()->streetAddress(),
            'notes' => fake()->optional()->sentence(),
            'status' => 'Pending',
        ];
    }
}
