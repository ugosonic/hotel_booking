<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TopUp extends Model
{
    protected $fillable = [
        'user_id',
        'method',
        'amount',
        'status',
        'bank_detail_id',
        'screenshot_path',
        'approved_by',
        'reference',
    ];
    
    // Relationship to user
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class,'user_id');
    }

    // Relationship to the staff user who approved
    public function approver()
    {
        return $this->belongsTo(\App\Models\User::class,'approved_by');
    }

    // Relationship to bank detail
    public function bankDetail()
    {
        return $this->belongsTo(\App\Models\BankDetail::class,'bank_detail_id');
    }
}
