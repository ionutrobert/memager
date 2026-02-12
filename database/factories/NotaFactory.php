<?php

namespace Database\Factories;

use App\Models\Member;
use App\Models\Nota;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class NotaFactory extends Factory
{
    protected $model = Nota::class;

    public function definition(): array
    {
        return [
            'member_id' => Member::factory(),
            'user_id'   => User::factory(),
            'nota'      => $this->faker->paragraph(),
            'created_at' => $this->faker->dateTimeBetween('-2 years', 'now'),
        ];
    }
}
