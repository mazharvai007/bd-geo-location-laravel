<?php

namespace Mazharvai\BDGeoLocation\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Division extends Model
{
    protected $table = 'bd_divisions';

    protected $fillable = [
        'id',
        'name',
        'name_bn',
        'lat',
        'long',
    ];

    protected $casts = [
        'lat' => 'decimal:8',
        'long' => 'decimal:8',
    ];

    public $incrementing = false;

    protected $keyType = 'string';

    /**
     * Get all districts for the division.
     */
    public function districts(): HasMany
    {
        return $this->hasMany(District::class, 'division_id');
    }
}
