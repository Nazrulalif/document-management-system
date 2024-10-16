<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\folder>
 */
class folderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'uuid' => $this->faker->uuid(),
            'folder_name' => $this->faker->word(), // Changed to word for better folder name
            'created_by' => $this->faker->randomElement(['1', '2', '3']),
            'org_guid' => $this->faker->randomElement(['1', '3', '5']),
        ];
    }
}
