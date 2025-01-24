<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Address extends Model
{
    protected $table = 'address';

    protected $primaryKey = 'id';

    protected $fillable = [
        'type',
        'street',
        'city',
        'state',
        'postalCode',
        'country',
    ];
}
