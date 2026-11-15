<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Amenity extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'icon',
    ];

    /**
     * Many-to-Many relation with Houses.
     */
    public function houses(): BelongsToMany
    {
        return $this->belongsToMany(House::class, 'house_amenity');
    }
}
