<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Member>
 */
class MemberFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $issueDate = $this->faker->dateTimeBetween('-10 years', 'now');
        $expireDate = (clone $issueDate)->modify('+10 years');

        $romanianCounties = [
            'Alba', 'Arad', 'Argeș', 'Bacău', 'Bihor', 'Bistrița-Năsăud', 'Botoșani', 'Brașov',
            'Brăila', 'București', 'Buzău', 'Caraș-Severin', 'Călărași', 'Cluj', 'Constanța',
            'Covasna', 'Dâmbovița', 'Dolj', 'Galați', 'Giurgiu', 'Gorj', 'Harghita', 'Hunedoara',
            'Ialomița', 'Iași', 'Ilfov', 'Maramureș', 'Mehedinți', 'Mureș', 'Neamț', 'Olt',
            'Prahova', 'Satu Mare', 'Sălaj', 'Sibiu', 'Suceava', 'Teleorman', 'Timiș', 'Tulcea',
            'Vaslui', 'Vâlcea', 'Vrancea',
        ];

        return [
            'CNP'                   => $this->generateValidCnp($issueDate),
            // Romanian CI serie: exactly 2 uppercase letters, e.g. "VX"
            'ci_serie'              => strtoupper($this->faker->lexify('??')),
            // Romanian CI number: 6 digits
            'ci_numar'              => $this->faker->numerify('######'),
            // Roughly matches Romanian issuing authority style
            'emis_de'               => 'SPCLEP ' . $this->faker->city(),
            'data_emitere'          => $issueDate->format('Y-m-d'),
            'data_expirare'         => $expireDate->format('Y-m-d'),
            'nume'                  => $this->faker->lastName(),
            'prenume'               => $this->faker->firstName(),
            'cetatenie'             => 'română',
            'nationalitate'         => 'română',
            'domiciliu'             => $this->faker->streetAddress(),
            'oras'                  => $this->faker->city(),
            'judet'                 => $this->faker->randomElement($romanianCounties),
            'oras_nastere'          => $this->faker->city(),
            'judet_nastere'         => $this->faker->randomElement($romanianCounties),
            'user_id'               => '1',
            //'contact_info' => null,
            //'workplace'        => null,
            'alte_info'             => null,
            'scan_carte_identitate' => null,

        ];
    }

    /**
     * Generate a valid Romanian CNP matching a reasonable birthdate.
     */
    protected function generateValidCnp(\DateTime $issueDate): string
    {
        // Sex/century digit: choose 5 for foreign resident or 1/2 for 1900s, 3/4 for 1800s, 7/8 for 2000s
        $year = (int) $issueDate->format('Y');
        $month = (int) $issueDate->format('m');
        $day = (int) $issueDate->format('d');

        if ($year >= 2000) {
            $s = $this->faker->randomElement([7, 8]);
            $yy = $year - 2000;
        } elseif ($year >= 1900) {
            $s = $this->faker->randomElement([1, 2]);
            $yy = $year - 1900;
        } else {
            $s = $this->faker->randomElement([3, 4]);
            $yy = $year - 1800;
        }

        $yy = str_pad((string) $yy, 2, '0', STR_PAD_LEFT);
        $mm = str_pad((string) $month, 2, '0', STR_PAD_LEFT);
        $dd = str_pad((string) $day, 2, '0', STR_PAD_LEFT);

        // county code 01-52 or 99
        $county = str_pad((string) $this->faker->numberBetween(1, 52), 2, '0', STR_PAD_LEFT);

        // unique number 001-999
        $n = str_pad((string) $this->faker->numberBetween(1, 999), 3, '0', STR_PAD_LEFT);

        $base = (string) $s . $yy . $mm . $dd . $county . $n;

        $control = [2,7,9,1,4,6,3,5,8,2,7,9];
        $sum = 0;
        for ($i = 0; $i < 12; $i++) {
            $sum += intval($base[$i]) * $control[$i];
        }
        $check = $sum % 11;
        if ($check == 10) $check = 1;

        return $base . $check;
    }
}
