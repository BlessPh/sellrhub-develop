<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Collection extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'type'
    ];

    public const TYPE_BEST_SELLER = 'best_seller';
    public const TYPE_FEATURED = 'featured';
    public const TYPE_NEW_ARRIVALS = 'new_arrivals';
    public const TYPE_PRODUCT_OF_DAY = 'product_of_day';

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class)
            ->withTimestamps();
    }
}
