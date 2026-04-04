<?php

namespace Mazharvai\BDGeoLocation\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Upazila extends Model
{
    protected $table = 'bd_upazilas';

    protected $fillable = [
        'id',
        'district_id',
        'name',
        'name_bn',
    ];

    public $incrementing = false;

    protected $keyType = 'string';

    /**
     * Get the district that the upazila belongs to.
     */
    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class, 'district_id');
    }

    /**
     * Get all unions for the upazila.
     */
    public function unions(): HasMany
    {
        return $this->hasMany(Union::class, 'upazila_id');
    }
}
