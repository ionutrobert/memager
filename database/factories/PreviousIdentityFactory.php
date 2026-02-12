<?php

namespace Database\Factories;

use App\Models\Member;
use App\Models\User;
use App\Models\PreviousIdentity;
use Illuminate\Database\Eloquent\Factories\Factory;

class PreviousIdentityFactory extends Factory
{
    protected $model = PreviousIdentity::class;

    public function definition(): array
    {
        return [
            'member_id'             => Member::factory(),
            'user_id'               => User::factory(),
            'ci_serie'              => $this->faker->regexify('[A-Z]{2}'),
            'ci_numar'              => $this->faker->numerify('######'),
            'emis_de'               => $this->faker->city(),
            'data_emitere'          => $this->faker->date(),
            'data_expirare'         => $this->faker->date('>now'),
            'nume'                  => $this->faker->lastName(),
            'prenume'               => $this->faker->firstName(),
            'cetatenie'             => $this->faker->country(),
            'nationalitate'         => $this->faker->country(),
            'domiciliu'             => $this->faker->address(),
            'oras'                  => $this->faker->city(),
            'judet'                 => $this->faker->randomElement(['Bucuresti', 'Cluj', 'Iasi', 'Timis', 'Constanta', 'Brasov', 'Galati', 'Dolj', 'Suceava', 'Prahova']),
            'oras_nastere'          => $this->faker->city(),
            'judet_nastere'         => $this->faker->randomElement(['Bucuresti', 'Cluj', 'Iasi', 'Timis', 'Constanta', 'Brasov', 'Galati', 'Dolj', 'Suceava', 'Prahova']),
            'scan_carte_identitate' => $this->faker->imageUrl(),
        ];
    }
}
