<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingGuest extends Model
{
    protected $table = 'booking_guests';

    protected $fillable = [
        'booking_id',
        'name',
        'phone',
        'address',
        'dob'
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }
}
