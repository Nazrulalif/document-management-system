<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\organization>
 */
class organizationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'uuid' =>  $this->faker->uuid(),
            'org_name' => $this->faker->company,
            'org_place' => $this->faker->streetName,
            'org_number' => $this->faker->companySuffix,
            'nature_of_business' => $this->faker->userName,
            'reg_date' => $this->faker->date(),
            'org_address' => $this->faker->streetAddress(),
            'is_operation' => 'Y',
        ];
    }
}
