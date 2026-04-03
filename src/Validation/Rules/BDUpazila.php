<?php

namespace Mazhar\BDGeoLocation\Validation\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Mazhar\BDGeoLocation\Facades\BDGeo;

class BDUpazila implements ValidationRule
{
    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $upazila = BDGeo::getUpazilaById($value);

        if (!$upazila) {
            $fail('The selected :attribute is invalid.');
        }
    }
}
