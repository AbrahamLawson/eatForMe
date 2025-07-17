<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

interface UserRepositoryInterface extends RepositoryInterface
{
    /**
     * Find user by email
     * 
     * @param string $email
     * @return \App\Models\User|null
     */
    public function findByEmail(string $email);
    
    /**
     * Get users by location proximity
     * 
     * @param float $latitude
     * @param float $longitude
     * @param float $radius in kilometers
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function findByLocation(float $latitude, float $longitude, float $radius = 10);
    
    /**
     * Find user with preferences and active availabilities
     * 
     * @param int $userId
     * @return \App\Models\User
     */
    public function findUserWithPreferencesAndAvailabilities(int $userId);
}
