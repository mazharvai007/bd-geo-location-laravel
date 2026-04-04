<?php

namespace Mazharvai\BDGeoLocation\Validation\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Mazharvai\BDGeoLocation\Facades\BDGeo;

class BDUnion implements ValidationRule
{
    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $union = BDGeo::getUnionById($value);

        if (!$union) {
            $fail('The selected :attribute is invalid.');
        }
    }
}
