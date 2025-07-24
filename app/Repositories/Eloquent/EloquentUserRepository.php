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
    
    /**
     * Find available users for matching based on filters
     * 
     * @param int $userId ID of the user looking for matches
     * @param array $filters Filters for matching (distance_max, activity_type, etc.)
     * @return \Illuminate\Support\Collection Collection of available users
     */
    public function findAvailableUsers(int $userId, array $filters = []): \Illuminate\Support\Collection
    {
        // Récupérer l'utilisateur courant pour obtenir sa position
        $currentUser = $this->findById($userId);
        
        if (!$currentUser || !$currentUser->latitude || !$currentUser->longitude) {
            return collect([]);
        }
        
        // Extraire les filtres
        $maxDistance = $filters['distance_max'] ?? 30; // 30km par défaut
        $activityType = $filters['activity_type'] ?? 'dinner';
        
        // Formule de Haversine pour calculer la distance
        $haversine = "(
            6371 * acos(
                cos(radians({$currentUser->latitude})) * 
                cos(radians(latitude)) * 
                cos(radians(longitude) - radians({$currentUser->longitude})) + 
                sin(radians({$currentUser->latitude})) * 
                sin(radians(latitude))
            )
        )";
        
        // Requête pour trouver les utilisateurs disponibles
        $availableUsers = $this->user
            ->selectRaw("*, {$haversine} as distance")
            ->where('id', '!=', $userId) // Exclure l'utilisateur courant
            ->whereNotNull('latitude') // Utilisateurs avec position géographique
            ->whereNotNull('longitude')
            ->whereRaw("{$haversine} < ?", [$maxDistance]) // Filtrer par distance
            ->with(['availabilities' => function($query) {
                $query->where('is_active', true);
            }])
            ->having('distance', '<=', $maxDistance)
            ->orderBy('distance')
            ->get();
        
        // Filtrer en PHP pour ne garder que les utilisateurs avec des disponibilités du bon type d'activité
        return $availableUsers->filter(function($user) use ($activityType) {
            // Filtrer les disponibilités par type d'activité en PHP
            $matchingAvailabilities = $user->availabilities->filter(function($availability) use ($activityType) {
                $preferences = $availability->preferences;
                
                // Si les préférences sont un string JSON, les décoder
                if (is_string($preferences)) {
                    $preferences = json_decode($preferences, true);
                }
                
                // Vérifier si l'activité correspond
                if (is_array($preferences) && isset($preferences['activity_type'])) {
                    return $preferences['activity_type'] === $activityType || $preferences['activity_type'] === 'any';
                }
                
                return false;
            });
            
            // Remplacer les availabilities de l'utilisateur par celles filtrées
            $user->setRelation('availabilities', $matchingAvailabilities);
            
            // Ne garder que les utilisateurs qui ont au moins une disponibilité correspondante
            return $matchingAvailabilities->isNotEmpty();
        });
    }
}
