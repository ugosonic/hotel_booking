<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationSetting extends Model
{
    use HasFactory;

    protected $table = 'notification_settings';

    protected $fillable = [
        'user_id',
        'login_notification',
        'password_changed_notification',
        'payment_error_notification',
        'payment_success_notification',
        'pending_topup_notification',
        'registration_welcome_notification',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
