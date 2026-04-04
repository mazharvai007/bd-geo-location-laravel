<?php

namespace Mazharvai\BDGeoLocation\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class District extends Model
{
    protected $table = 'bd_districts';

    protected $fillable = [
        'id',
        'division_id',
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
     * Get the division that the district belongs to.
     */
    public function division(): BelongsTo
    {
        return $this->belongsTo(Division::class, 'division_id');
    }

    /**
     * Get all upazilas for the district.
     */
    public function upazilas(): HasMany
    {
        return $this->hasMany(Upazila::class, 'district_id');
    }
}
