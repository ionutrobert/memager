<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\PreviousIdentity;
use App\Rules\Cnp as CnpRule;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CnpCheckController
{
    public function check(Request $request)
    {
        $cnp = (string) $request->query('cnp', '');

        if ($cnp === '') {
            return response()->json(['valid' => false, 'message' => 'Enter CNP']);
        }

        // quick format check
        if (!preg_match('/^[0-9]{13}$/', $cnp)) {
            return response()->json(['valid' => false, 'message' => 'CNP must be 13 digits']);
        }

        $rule = new CnpRule();
        $valid = $rule->passes('CNP', $cnp);

        if (! $valid) {
            return response()->json(['valid' => false, 'message' => 'Invalid CNP checksum or format']);
        }

        $member = Member::where('CNP', $cnp)->first();

        if ($member) {
            $pi = PreviousIdentity::where('member_id', $member->id)
                ->orderByDesc('data_emitere')
                ->first();

            $name = $member->nume . ' ' . $member->prenume;
            if ($pi) {
                try {
                    if (Carbon::createFromFormat('Y-m-d', $pi->data_expirare)->gt(Carbon::today())) {
                        $name = trim(($pi->nume ?? $member->nume) . ' ' . ($pi->prenume ?? $member->prenume));
                    }
                } catch (\Throwable $e) {
                    // ignore parse errors
                }
            }

            return response()->json(['valid' => true, 'exists' => true, 'name' => $name]);
        }

        return response()->json(['valid' => true, 'exists' => false]);
    }
}
