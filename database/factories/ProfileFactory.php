<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Profile>
 */
class ProfileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Coordonnées aléatoires en France (approximativement)
        $latitude = $this->faker->latitude(42.0, 51.0);
        $longitude = $this->faker->longitude(-5.0, 8.0);
        
        // Préférences alimentaires possibles
        $foodPreferences = $this->faker->randomElements(
            [
                'vegetarian', 'vegan', 'pescatarian', 'gluten_free', 
                'lactose_free', 'keto', 'paleo', 'halal', 'kosher',
                'italian', 'french', 'japanese', 'chinese', 'mexican', 
                'indian', 'thai', 'mediterranean'
            ],
            $this->faker->numberBetween(1, 5)
        );
        
        return [
            'user_id' => User::factory(),
            'bio' => $this->faker->paragraph(),
            'food_preferences' => $foodPreferences,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'avatar_url' => 'https://i.pravatar.cc/300?u=' . $this->faker->uuid,
        ];
    }
}
