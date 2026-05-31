<?php

namespace App\Listeners;

use App\Events\RideStatusUpdated;
use App\Models\RideRequest;
use App\Services\RideStatusPublisher;

class PublishRideStatusUpdate
{
    public function __construct(private readonly RideStatusPublisher $rideStatusPublisher) {}

    /**
     * Handle the event.
     */
    public function handle(RideStatusUpdated $event): void
    {
        $rideRequest = $event->rideRequest->loadMissing('rider:id,name');
        $payload = $this->payload($rideRequest);

        $this->rideStatusPublisher->publish(
            $this->topicForCustomer((int) $rideRequest->customer_id),
            $payload,
        );

        $this->rideStatusPublisher->publish(
            $this->adminTopic(),
            $payload,
        );
    }

    private function topicForCustomer(int $customerId): string
    {
        $topicRoot = trim((string) config('services.mqtt.topics.ride_status', 'ride/status'), '/');

        return "{$topicRoot}/{$customerId}";
    }

    private function adminTopic(): string
    {
        return trim((string) config('services.mqtt.topics.admin_ride_status', 'ride/status/admin'), '/');
    }

    /**
     * @return array{
     *     ride_id: int,
     *     customer_id: int,
     *     rider_id: int|null,
     *     rider: array{id: int, name: string}|null,
     *     status: string,
     *     pickup_location: string,
     *     destination_location: string,
     *     updated_at: string|null
     * }
     */
    private function payload(RideRequest $rideRequest): array
    {
        return [
            'ride_id' => (int) $rideRequest->id,
            'customer_id' => (int) $rideRequest->customer_id,
            'rider_id' => $rideRequest->rider_id !== null ? (int) $rideRequest->rider_id : null,
            'rider' => $rideRequest->rider !== null
                ? [
                    'id' => (int) $rideRequest->rider->id,
                    'name' => $rideRequest->rider->name,
                ]
                : null,
            'status' => $rideRequest->status,
            'pickup_location' => $rideRequest->pickup_location,
            'destination_location' => $rideRequest->destination_location,
            'updated_at' => $rideRequest->updated_at?->toISOString(),
        ];
    }
}
