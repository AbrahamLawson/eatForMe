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
        $gender = fake()->randomElement(['male', 'female']);
        
        return [
            'name' => $gender === 'male' ? fake()->name('male') : fake()->name('female'),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'gender' => $gender,
            'preferences' => [
                'gender' => fake()->randomElement(['male', 'female', 'any']),
                'age_min' => fake()->numberBetween(18, 30),
                'age_max' => fake()->numberBetween(30, 50),
                'favorite_activities' => fake()->randomElements(
                    ['sports', 'music', 'movies', 'reading', 'cooking', 'travel', 'art', 'gaming'],
                    fake()->numberBetween(1, 4)
                )
            ],
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
