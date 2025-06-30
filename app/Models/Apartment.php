<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Apartment extends Model
{
    protected $fillable = [
        'apartment_name',
        'has_rooms','num_rooms',
        'has_toilets','num_toilets',
        'has_sittingroom','num_sittingrooms',
        'has_kitchen','num_kitchens',
        'has_balcony','num_balconies',
        'free_wifi','water','electricity',
        'price','additional_info',
    ];

    // If you want to link to Bookings:
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
