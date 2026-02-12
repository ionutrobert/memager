<?php

namespace Database\Factories;

use App\Models\MemberWorkplaceDetail;
use App\Models\User;
use App\Models\Workplace;
use Illuminate\Database\Eloquent\Factories\Factory;

class MemberWorkplaceDetailFactory extends Factory
{
    protected $model = MemberWorkplaceDetail::class;

    public function definition(): array
    {
        return [
            'member_id'                  => null,
            'workplace_id'               => Workplace::factory(),
            'data_informatie'            => $this->faker->optional()->date(),
            'tip_informatie'             => $this->faker->randomElement(['Revisal', 'Adeverinta']),
            'data_incepere_cim'          => $this->faker->optional()->date(),
            'data_incetare_cim'          => $this->faker->optional()->date(),
            'tip_durata_cim'             => $this->faker->randomElement(['Nedeterminata', 'Determinata']),
            'tip_norma_cim'              => $this->faker->randomElement(['Norma intreaga', 'Norma partiala']),
            'functie'                    => $this->faker->jobTitle(),
            'salariu_de_baza_lunar_brut' => $this->faker->optional()->numberBetween(2000, 10000),
            'sporuri_indemnizatii_adaosuri' => $this->faker->optional()->numberBetween(0, 2000),
            'scan_document'              => $this->faker->optional()->word() . '.pdf',
            'user_id'                    => User::factory(),
        ];
    }
}
