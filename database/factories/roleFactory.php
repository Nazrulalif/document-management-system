<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\role>
 */
class roleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {

        return [
            'id'        => '',  // Will be filled in seeder
            'role_name'        => '',  // Will be filled in seeder
            'uuid'             => Str::uuid(),  // Generate unique UUID
            'role_description' => $this->faker->sentence(),  // Random sentence for description
        ];
    }
}
