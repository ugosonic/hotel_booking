<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserActivity extends Model
{
    protected $table = 'user_activities';
    
    // If you want all columns fillable:
    protected $fillable = [
        'user_id',
        'type',
        'description',
    ];

    // If you prefer controlling timestamps:
    // public $timestamps = true;

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }
}
