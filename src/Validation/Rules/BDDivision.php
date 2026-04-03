<?php

namespace Mazhar\BDGeoLocation\Validation\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Mazhar\BDGeoLocation\Facades\BDGeo;

class BDDivision implements ValidationRule
{
    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $division = BDGeo::getDivisionById($value);

        if (!$division) {
            $fail('The selected :attribute is invalid.');
        }
    }
}
