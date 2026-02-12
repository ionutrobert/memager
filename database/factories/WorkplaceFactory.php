<?php

namespace Database\Factories;

use App\Models\Member;
use App\Models\User;
use App\Models\Workplace;
use Illuminate\Database\Eloquent\Factories\Factory;

class WorkplaceFactory extends Factory
{
    protected $model = Workplace::class;

    public function definition(): array
    {
        return [
            'user_id'   => User::factory(),
            'employer'  => $this->faker->company(),
            'CUI'       => $this->faker->optional()->bothify('RO#########'),
            'adresa'    => $this->faker->address(),
            'oras'      => $this->faker->city(),
            'judet'     => $this->faker->randomElement(['Bucuresti', 'Cluj', 'Iasi', 'Timis', 'Constanta', 'Brasov', 'Galati', 'Dolj', 'Suceava', 'Prahova']),
            'contact'   => json_encode(['contact' => $this->faker->phoneNumber()]),
            'info'      => json_encode(['contact_person' => $this->faker->name()]),
        ];
    }
}
