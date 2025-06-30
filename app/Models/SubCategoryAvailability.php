<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubCategoryAvailability extends Model
{
    protected $fillable = [
        'sub_category_id',
        'date',
        'slots',
        'is_unavailable',
    ];

    public function subCategory(): BelongsTo
    {
        return $this->belongsTo(SubCategory::class);
    }
}
