<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class House extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'price_per_night',
        'capacity',
        'area',
        'image_url',
        'is_featured',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'price_per_night' => 'decimal:2',
            'capacity' => 'integer',
            'area' => 'integer',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Category of the house.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Many-to-Many relation with amenities.
     */
    public function amenities(): BelongsToMany
    {
        return $this->belongsToMany(Amenity::class, 'house_amenity');
    }

    /**
     * Bookings for this house.
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Reviews for this house.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Scope for active houses.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for featured houses on homepage.
     */
    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true)->where('is_active', true);
    }

    /**
     * Computed average rating of published reviews.
     */
    public function getAverageRatingAttribute(): float
    {
        $avg = $this->reviews()->where('is_published', true)->avg('rating');
        return $avg ? round((float)$avg, 1) : 5.0;
    }
}
