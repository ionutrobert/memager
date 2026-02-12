<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class Cnp implements Rule
{
    /**
     * Determine if the validation rule passes.
     */
    public function passes($attribute, $value): bool
    {
        // must be 13 digits
        if (!preg_match('/^[0-9]{13}$/', $value)) {
            return false;
        }

        $digits = str_split($value);

        // checksum algorithm
        $control = [2,7,9,1,4,6,3,5,8,2,7,9];

        $sum = 0;
        for ($i = 0; $i < 12; $i++) {
            $sum += intval($digits[$i]) * $control[$i];
        }

        $check = $sum % 11;
        if ($check == 10) {
            $check = 1;
        }

        return intval($digits[12]) === $check;
    }

    /**
     * Get the validation error message.
     */
    public function message(): string
    {
        return 'The :attribute is not a valid CNP.';
    }
}
