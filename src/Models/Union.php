<?php

namespace Mazhar\BDGeoLocation\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Union extends Model
{
    protected $table = 'bd_unions';

    protected $fillable = [
        'id',
        'upazila_id',
        'name',
        'name_bn',
    ];

    public $incrementing = false;

    protected $keyType = 'string';

    /**
     * Get the upazila that the union belongs to.
     */
    public function upazila(): BelongsTo
    {
        return $this->belongsTo(Upazila::class, 'upazila_id');
    }
}
