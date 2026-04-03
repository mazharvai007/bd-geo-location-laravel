<?php

namespace Mazhar\BDGeoLocation\Validation\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Mazhar\BDGeoLocation\Facades\BDGeo;

class BDDistrict implements ValidationRule
{
    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $district = BDGeo::getDistrictById($value);

        if (!$district) {
            $fail('The selected :attribute is invalid.');
        }
    }
}
