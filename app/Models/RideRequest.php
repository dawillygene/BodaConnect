<?php

namespace App\Models;

use Database\Factories\RideRequestFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'customer_id',
    'rider_id',
    'pickup_location',
    'destination_location',
    'notes',
    'status',
])]
class RideRequest extends Model
{
    /** @use HasFactory<RideRequestFactory> */
    use HasFactory;

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function rider(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rider_id');
    }
}
