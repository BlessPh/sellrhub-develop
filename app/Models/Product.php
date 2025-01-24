<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_name',
        'product_description',
        'product_price',
        'product_video',
        'product_quantity',
        'is_featured',
        'weight',
        'size',
        'views_count',
        'category_id',
        'shop_id'
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImageUrl::class);
    }

    public function colors(): HasMany
    {
        return $this->hasMany(ProductColor::class);
    }

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    public function promotions(): BelongsToMany
    {
        return $this->belongsToMany(Promotion::class)
            ->withPivot('promotional_price')
            ->withTimestamps();
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class, 'product_id');
    }

    public function orderItems(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function viewers()
    {
        return $this->belongsToMany(User::class, 'user_product_views')
            ->withTimestamps();
    }

    protected $casts = [
        'product_images' => 'array',
        'product_colors' => 'array',
    ];
}
