<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Apartment;


class Payment extends Model
{
    protected $fillable = [
        'booking_id', 'amount', 'payment_method', 
        'collected_by', 'status', 'reference',
        // e.g. staff_name, client_name, ...
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }
    
    public function apartment()
{
    return $this->belongsTo(Apartment::class);
}

}
