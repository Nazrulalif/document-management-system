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
            // 'uuid' =>  $this->faker->uuid(),
            // 'full_name' => 'Super',
            // 'email' => 'Supers@gmail.com',
            // 'email_verified_at' => now(),
            // 'password' => '$2y$12$jA1DuUJhwzPD4.0wgJHfXOSi0G3bAMjkysIL65pD.Yfi6HaCEm.Ni',
            // 'remember_token' => Str::random(10),
            // 'ic_number' => '1011920394',
            // 'position' => 'Director',
            // 'role_guid' => '1',

            'uuid' =>  $this->faker->uuid(),
            'full_name' => $this->faker->userName(),
            'email' => $this->faker->email(),
            'email_verified_at' => now(),
            'password' => '$2y$12$jA1DuUJhwzPD4.0wgJHfXOSi0G3bAMjkysIL65pD.Yfi6HaCEm.Ni',
            'remember_token' => Str::random(10),
            'ic_number' => '12345',
            'position' => $this->faker->randomLetter(),
            'role_guid' => '1',
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
