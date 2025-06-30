<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemGain extends Model
{
    protected $fillable = [
        'booking_id',
        'user_id',
        'type',
        'amount',
    ];

    // If you want relationships:
    public function booking()
    {
        return $this->belongsTo(\App\Models\Booking::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}
