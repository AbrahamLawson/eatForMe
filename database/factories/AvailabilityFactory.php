<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Availability>
 */
class AvailabilityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Activités possibles
        $activities = ['eat', 'drink', 'chat'];
        $selectedActivity = $this->faker->randomElement($activities);
        
        // Coordonnées aléatoires en France (approximativement)
        $latitude = $this->faker->latitude(42.0, 51.0);
        $longitude = $this->faker->longitude(-5.0, 8.0);
        
        // Préférences pour la disponibilité
        $preferences = [
            'activity' => $selectedActivity,
            'gender' => $this->faker->randomElement(['male', 'female', 'any']),
            'favorite_activities' => $this->faker->randomElements(
                ['sports', 'music', 'movies', 'reading', 'cooking', 'travel', 'art', 'gaming'],
                $this->faker->numberBetween(1, 4)
            )
        ];
        
        return [
            'user_id' => User::factory(),
            'is_active' => $this->faker->boolean(70), // 70% de chance d'être actif
            'latitude' => $latitude,
            'longitude' => $longitude,
            'preferences' => $preferences,
        ];
    }
    
    /**
     * Indicate that the availability is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }
    
    /**
     * Indicate that the availability is for a specific activity.
     */
    public function forActivity(string $activity): static
    {
        return $this->state(function (array $attributes) use ($activity) {
            $preferences = $attributes['preferences'] ?? [];
            $preferences['activity'] = $activity;
            
            return [
                'preferences' => $preferences,
            ];
        });
    }
}
