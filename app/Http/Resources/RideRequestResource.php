<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RideRequestResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'pickup_location' => $this->pickup_location,
            'destination_location' => $this->destination_location,
            'notes' => $this->notes,
            'status' => $this->status,
            'customer_id' => $this->customer_id,
            'rider_id' => $this->rider_id,
            'customer' => UserResource::make($this->whenLoaded('customer')),
            'rider' => UserResource::make($this->whenLoaded('rider')),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
