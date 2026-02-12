<?php

namespace Database\Factories;

use App\Models\DiscutieTelefonica;
use App\Models\Member;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class DiscutieTelefonicaFactory extends Factory
{
    protected $model = DiscutieTelefonica::class;

    public function definition(): array
    {
        return [
            'member_id'           => Member::factory(),
            'contact_info_id'     => null,
            'participant_discutie' => null,
            'rezumat'             => $this->faker->paragraph(),
            'data_discutie'       => $this->faker->dateTimeBetween('-1 year', 'now'),
            'user_id'             => User::factory(),
        ];
    }
}
