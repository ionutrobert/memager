<?php

namespace App\Models;

use App\Support\DateHelper;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Debt extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'member_id',
        'user_id',
        'data_acordare',
        'suma',
        'procent',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function payment()
    {
        return $this->hasMany(Payment::class)->orderBy('data');
    }

    public function calculateDays($prevDate, $currentDate)
    {
        return DateHelper::diffInDays($prevDate, $currentDate);
    }

    public function calculateInterest($payment, $days, $soldRamasRata)
    {

        // Calculate interest
        $interestRate   = $payment->debt->procent / 100;
        $interestPerDay = $interestRate / 365;

        $interest = $days * $interestPerDay * $soldRamasRata;

        return round($interest, 2);
    }

    /**
     * REMAINING DEBT
     * @param $id (debt id)
     * Shows final remaining debt after all payments
     * Returns ->balance and ->interest
     *
     */
    public function remainingDebt($id)
    {

        $debt = Debt::where('id', $id)->with('payment')->orderBy('data', 'ASC')->first();

        // INITIALIZING AS THERE IS NO PREVIEWS PAYMENT DATE
        $prevDate = $debt->data_acordare;


        // INITIALIZING AS THERE IS NO PREVIEWS SOLD RAMAS
        $soldRamasRata    = $debt->suma;
        $soldRamasDobanda = 0;



        foreach ($debt->payment as $payment) :

            $days = $this->calculateDays($payment->data, $prevDate);

            $dobandaCalculata = $this->calculateInterest($payment, $days, $soldRamasRata);

            $soldRamasDobanda = $soldRamasDobanda + $dobandaCalculata - $payment->suma;

            // IF SOLD RAMAS DOBANDA IS NEGATIVE, SUBSTRACT FROM RATA
            if ($soldRamasDobanda < 0) {
                $soldRamasRata    = $soldRamasRata + $soldRamasDobanda;
                $soldRamasDobanda = 0;
            }

            // $soldRamasRata
            // $soldRamasDobanda

            $prevDate = $payment->data;

        endforeach;
        $this->balance = $soldRamasRata;
        $this->interest = $soldRamasDobanda;

        return $this;
    }
}
