<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that can be mass assigned (when using User::create()).
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role', // <--- Important! So "role" can be saved
    ];

    /**
     * Hide sensitive fields from JSON, etc.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Cast fields to proper data types.
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed', // "hashed" is new in Laravel 10+, otherwise use 'string'
    ];

    /**
     * True if the user has a 'staff' role.
     */
    public function isStaff()
    {
        return $this->role === 'staff';
    }

    public function accountBalance()
{
    return $this->hasOne(\App\Models\AccountBalance::class, 'user_id');
}

public function notificationSetting()
{
    return $this->hasOne(NotificationSetting::class);
}


}
