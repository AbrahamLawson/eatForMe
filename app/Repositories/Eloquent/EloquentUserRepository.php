<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

final class EloquentUserRepository extends AbstractEloquentRepository implements UserRepositoryInterface
{
    /**
     * @param User $user
     */
    public function __construct(
        private User $user
    ) {
        $this->model = $user;
    }

    /**
     * Find user by email
     * 
     * @param string $email
     * @return User|null
     */
    public function findByEmail(string $email): ?User
    {
        return $this->user->where('email', $email)->first();
    }
    
    /**
     * Get users by location proximity
     * 
     * @param float $latitude
     * @param float $longitude
     * @param float $radius in kilometers
     * @return Collection
     */
    public function findByLocation(float $latitude, float $longitude, float $radius = 10): Collection
    {
        // Formule de Haversine pour calculer la distance
        $haversine = "(
            6371 * acos(
                cos(radians($latitude)) * 
                cos(radians(latitude)) * 
                cos(radians(longitude) - radians($longitude)) + 
                sin(radians($latitude)) * 
                sin(radians(latitude))
            )
        )";
        
        return $this->user
            ->selectRaw("*, {$haversine} as distance")
            ->whereRaw("{$haversine} < ?", [$radius])
            ->orderBy('distance')
            ->get();
    }
    
    /**
     * Find user with active availabilities
     * 
     * @param int $userId
     * @return User
     */
    public function findUserWithPreferencesAndAvailabilities(int $userId): User
    {
        return $this->user
            ->with(['availabilities' => function($query) {
                $query->where('is_active', true)
                      ->orderBy('created_at', 'desc');
            }])
            ->findOrFail($userId);
    }
}
