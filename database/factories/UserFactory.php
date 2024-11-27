<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'uuid' => $this->faker->uuid(),
            'full_name' => $this->faker->name(),  // Generate a random full name
            'email' => $this->faker->unique()->safeEmail(),  // Ensure unique email
            'email_verified_at' => now(),
            'password' => bcrypt('123'),  // Encrypt the password '123'
            'remember_token' => Str::random(10),
            'ic_number' => $this->faker->unique()->numerify('######'),  // Generate a fake IC number
            'is_active' => 'Y',
            'nationality' => $this->faker->randomElement(['Malaysia', 'Singapore', 'Indonesia', 'Brunei']),  // Include other nationalities for variety
            'gender' => $this->faker->randomElement(['male', 'female']),
            'race' => $this->faker->randomElement(['Malay', 'Chinese', 'Indian', 'Others']),  // Use specific races
            'position' => $this->faker->randomElement(['Manager', 'Developer', 'Designer', 'Tester']),  // Use common job positions
            'role_guid' => $this->faker->randomElement(['1', '2', '3', '4']),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn(array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
