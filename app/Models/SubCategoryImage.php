<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubCategoryImage extends Model
{
    protected $fillable = [
        'sub_category_id',
        'image_path'
    ];

    public function subCategory(): BelongsTo
    {
        return $this->belongsTo(SubCategory::class);
    }
}
