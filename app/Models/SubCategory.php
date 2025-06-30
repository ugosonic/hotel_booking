<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubCategory extends Model
{
    protected $fillable = [
        'category_id',
        'name',
        'price',
        'file_path',
        'description',
        'has_rooms','num_rooms',
        'has_toilets','num_toilets',
        'has_sittingroom','num_sittingrooms',
        'has_kitchen','num_kitchens',
        'has_balcony','num_balconies',
        'free_wifi','water','electricity',
        'additional_info'
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Relationship to sub_category_images table
     */
    public function images(): HasMany
    {
        return $this->hasMany(SubCategoryImage::class);
    }

    /**
     * Relationship to sub_category_availabilities table
     */
    public function availabilities(): HasMany
    {
        return $this->hasMany(SubCategoryAvailability::class);
    }
}

