<?php

namespace Database\Factories;

use App\Models\Debt;
use App\Models\Member;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class DebtFactory extends Factory
{
    protected $model = Debt::class;

    public function definition(): array
    {
        return [
            'member_id'     => Member::factory(),
            'user_id'       => User::factory(),
            'data_acordare' => $this->faker->date(),
            'suma'          => $this->faker->numberBetween(100, 10000),
            'procent'       => $this->faker->numberBetween(1, 30),
        ];
    }
}
