<?php

namespace App\Support;

use Carbon\Carbon;

class DateHelper
{
    /**
     * Calculate the difference in days between two dates.
     *
     * Accepts:
     * - Carbon instances
     * - Strings in d/m/Y (e.g. 15/08/2024)
     * - Strings parsable by Carbon::parse (e.g. 2024-08-15 or 2024-08-15 00:00:00)
     */
    public static function diffInDays($prevDate, $currentDate): int
    {
        // Normalize $prevDate
        if (! $prevDate instanceof Carbon) {
            if (is_string($prevDate) && str_contains($prevDate, '/')) {
                // e.g. 15/08/2024
                $prevDate = Carbon::createFromFormat('d/m/Y', $prevDate);
            } else {
                // e.g. 2024-08-15 or 2024-08-15 00:00:00
                $prevDate = Carbon::parse($prevDate);
            }
        }

        // Normalize $currentDate
        if (! $currentDate instanceof Carbon) {
            if (is_string($currentDate) && str_contains($currentDate, '/')) {
                $currentDate = Carbon::createFromFormat('d/m/Y', $currentDate);
            } else {
                $currentDate = Carbon::parse($currentDate);
            }
        }

        return $currentDate->diffInDays($prevDate);
    }
}

