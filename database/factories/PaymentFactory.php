<?php

namespace Database\Factories;

use App\Models\Debt;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        return [
            'debt_id'    => Debt::factory(),
            'user_id'    => User::factory(),
            'data'       => $this->faker->date(),
            'suma'       => $this->faker->numberBetween(10, 5000),
            'act'        => $this->faker->bothify('ACT-####'),
        ];
    }
}
