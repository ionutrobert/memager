<?php

namespace Database\Factories;

use App\Models\ContactInfo;
use App\Models\Member;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContactInfoFactory extends Factory
{
    protected $model = ContactInfo::class;

    public function definition(): array
    {
        $type = $this->faker->randomElement(['telefon', 'email', 'adresa_corespondenta']);

        $info = match($type) {
            'telefon' => $this->faker->phoneNumber(),
            'email' => $this->faker->safeEmail(),
            'adresa_corespondenta' => $this->faker->address(),
        };

        return [
            'member_id' => Member::factory(),
            'tip_info'  => $type,
            'info'      => $info,
            'user_id'   => User::factory(),
        ];
    }

    public function telefon(): self
    {
        return $this->state([
            'tip_info' => 'telefon',
            'info' => $this->faker->phoneNumber(),
        ]);
    }

    public function email(): self
    {
        return $this->state([
            'tip_info' => 'email',
            'info' => $this->faker->safeEmail(),
        ]);
    }

    public function adresa(): self
    {
        return $this->state([
            'tip_info' => 'adresa_corespondenta',
            'info' => $this->faker->address(),
        ]);
    }
}
