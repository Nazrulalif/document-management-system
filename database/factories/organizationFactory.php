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
        $natureOfBusiness = [
            'Retail',
            'Wholesale',
            'Manufacturing',
            'Construction',
            'Information Technology',
            'Healthcare',
            'Financial Services',
            'Real Estate',
            'Hospitality',
            'Transportation',
            'Marketing',
            'Education',
            'Consulting',
            'E-commerce',
            'Telecommunications',
            'Non-profit'
        ];

        return [
            'uuid' => $this->faker->uuid(),
            'org_name' => $this->faker->company,
            'org_place' => $this->faker->state,  // Generate a fake state
            'org_number' => $this->faker->bothify('ORG-###??'),  // Generate a string with format ORG-###??
            'nature_of_business' => $this->faker->randomElement($natureOfBusiness),  // Generate a fake nature of business
            'reg_date' => $this->faker->date(),
            'org_address' => $this->faker->streetAddress(),
            'is_operation' => 'Y',
            'is_parent' => 'N',
        ];
    }
}
