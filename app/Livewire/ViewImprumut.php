<?php

namespace App\Livewire;

use App\Models\Debt;
use App\Models\Member;
use App\Support\DateHelper;
use Carbon\Carbon;
use Livewire\Component;

class ViewImprumut extends Component
{

    public $debts;


    public function mount($record): void
{
        $this->debts = Debt::where('member_id', $record->id)->with('payment')->orderBy('data', 'ASC')->get();


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

    public function calculateTodaysSummary($debt, $lastPaymentDate, $soldRamas, $soldRamasDobanda)
    {
        $today = Carbon::today();
        $days = DateHelper::diffInDays($lastPaymentDate, $today->toDateString());

        $interestRate = $debt->procent / 100;
        $interestPerDay = $interestRate / 365;
        $todayInterest = $days * $interestPerDay * $soldRamas;

        return [
            'date' => $today->toDateString(),
            'days' => $days,
            'interest' => round($todayInterest, 2),
            'soldRamas' => $soldRamas,
            'soldRamasDobanda' => round($soldRamasDobanda + $todayInterest, 2),
        ];
    }


    public function render()
    {
        return view('livewire.view-imprumut');
    }
}
