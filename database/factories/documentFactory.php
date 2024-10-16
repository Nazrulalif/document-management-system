<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\document>
 */
class documentFactory extends Factory
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
            'latest_version_guid' => '4395acf4-29d6-4de7-b60f-82707bab4ced',
            'version_limit' => '5',
            'doc_name' => $this->faker->word(), // Unique word for document name
            'doc_title' => $this->faker->sentence(2), // Generate a single sentence for title
            'doc_summary' => $this->faker->paragraph(), // Generate a single sentence for title
            'doc_description' => $this->faker->paragraph(), // Generate a paragraph for description
            'doc_type' => $this->faker->randomElement(['pdf', 'xlsx', 'docx', 'doc', 'csv', 'images']),
            'doc_author' => $this->faker->name(),
            'doc_keyword' => implode(', ', $this->faker->words(3)),
            'upload_by' => $this->faker->randomElement(['1', '2', '3']),
            'org_guid' => $this->faker->randomElement(['1', '3', '5']),
        ];
    }
}
