<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Booking extends Model
{
    use HasFactory;

    // If your table is named "bookings" and follows
    // standard conventions, no need to specify $table = 'bookings';

    protected $fillable = [
        'staff_id',
        'client_id',
        'client_name', // <-- ADD THIS
        'guest_name',
        'guest_email',
        'guest_address',
        'guest_phone',
        'guest_dob',
        'doc_type',
        'doc_number',
        'doc_upload',
        'sub_category_id',
        'start_date',
        'end_date',
        'nights',
        'status',
    
        // If you’re also storing price & total_amount this way:
        'price',
        'total_amount',
    ];

    public function payments()
{
    return $this->hasMany(\App\Models\Payment::class, 'booking_id');
}

public function cancellation()
{
    return $this->hasOne(BookingCancellation::class);
}

public function systemGains()
{
    return $this->hasMany(SystemGain::class);
}

    /**
     * Relation to a SubCategory
     */
    public function subCategory()
    {
        return $this->belongsTo(\App\Models\SubCategory::class, 'sub_category_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // Only generate if not already set (e.g. staff might override)
            if (empty($model->public_id)) {
                // Generate a random 8‐character string of letters & numbers
                $model->public_id = strtoupper(Str::random(8));
            }
        });
    }

    /**
     * Relation to client user (if you store a client_id).
     */
    public function client()
    {
        return $this->belongsTo(\App\Models\User::class, 'client_id');
    }

    /**
     * Relation to staff user (if you store a staff_id).
     */
    public function staff()
    {
        return $this->belongsTo(\App\Models\User::class, 'staff_id');
    }
}
