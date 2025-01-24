<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Promotion extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'promo_code',
        'discount_percentage',
        'shop_id',
        'starts_at',
        'ends_at',
        'status'
    ];

    protected $casts = [
        'images' => 'array',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'discount_percentage' => 'float'
    ];

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImageUrl::class);
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class)
            ->withPivot('promotional_price')
            ->withTimestamps();
    }

    public static function generatePromoCode(string $initials, int $length = 8): string
    {
        $baseCode = Str::upper(Str::substr($initials, 0, 3));
        $remainingLength = $length - strlen($baseCode);
        $randomString = Str::random($remainingLength);

        return $baseCode . $randomString;
    }

    public function calculatePromotionalPrice(float $originalPrice): float
    {
        return $originalPrice * (1 - ($this->discount_percentage / 100));
    }
}
