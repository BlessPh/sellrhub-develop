<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PaymentMethod extends Model
{
    use HasFactory;

    protected $table = 'payment_method';

    protected $primaryKey = 'id';

    protected $fillable = [
        'method_name'
    ];

    public function shops(): BelongsTo
    {
        return $this->belongsTo(Shop::class, 'shop_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
