<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingCancellation extends Model
{
    protected $fillable = [
        'booking_id',
        'user_id',
        'canceled_by',
        'canceled_amount',
        'refunded_amount',
        'system_gain',
        'canceled_at',
    ];

    // relationships if needed
    public function booking()
    {
        return $this->belongsTo(\App\Models\Booking::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    public function canceledBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'canceled_by');
    }
}
