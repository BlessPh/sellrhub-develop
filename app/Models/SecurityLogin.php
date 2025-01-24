<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SecurityLogin extends Model
{
    protected $fillable = [
        'user_id',
        'ip_address',
        'device_info',
        'login_date',
        'logout_date'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
