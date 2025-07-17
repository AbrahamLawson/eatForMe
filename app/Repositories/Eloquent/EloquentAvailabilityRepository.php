<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\DTO\AvailabilityDTO;
use App\Models\Availability;
use App\Repositories\Interfaces\AvailabilityRepositoryInterface;

final class EloquentAvailabilityRepository extends AbstractEloquentRepository implements AvailabilityRepositoryInterface
{
    /**
     * @param Availability $availability
     */
    public function __construct(
        private Availability $availability
    ) {
        $this->model = $availability;
    }

    /**
     * Crée une nouvelle disponibilité
     *
     * @param AvailabilityDTO $availabilityDTO
     * @return AvailabilityDTO
     */
    public function createFromDTO(AvailabilityDTO $availabilityDTO): AvailabilityDTO
    {
        $availability = $this->availability->create([
            'user_id' => auth()->id(), // Utiliser l'utilisateur authentifié ou un autre moyen de récupérer l'ID utilisateur
            'is_active' => $availabilityDTO->isActive(),
            'latitude' => $availabilityDTO->getLocationLat(),
            'longitude' => $availabilityDTO->getLocationLng(),
            'preferences' => $availabilityDTO->getPreferences() ? json_encode($availabilityDTO->getPreferences()) : null,
        ]);

        return AvailabilityDTO::fromArray($availability->toArray());
    }

    /**
     * Trouve une disponibilité par son ID et retourne un DTO
     *
     * @param int $id
     * @return AvailabilityDTO|null
     */
    public function findByIdAsDTO(int $id): ?AvailabilityDTO
    {
        $availability = $this->findById($id);
        
        if (!$availability) {
            return null;
        }
        
        return AvailabilityDTO::fromArray($availability->toArray());
    }

    /**
     * Trouve les disponibilités d'un utilisateur
     *
     * @param int $userId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function findByUserId(int $userId): \Illuminate\Database\Eloquent\Collection
    {
        return $this->availability
            ->where('user_id', $userId)
            ->get();
    }
    
    /**
     * Find active availabilities.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function findActiveInTimeRange(string $startTime = null, string $endTime = null): \Illuminate\Database\Eloquent\Collection
    {
        return $this->availability
            ->where('is_active', true)
            ->get();
    }
    
    /**
     * Find availabilities near a specific location.
     *
     * @param float $latitude
     * @param float $longitude
     * @param float $radiusInKm
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function findNearLocation(float $latitude, float $longitude, float $radiusInKm = 5.0): \Illuminate\Database\Eloquent\Collection
    {
        // Calcul de la distance avec la formule Haversine
        return $this->availability
            ->select('*')
            ->selectRaw(
                '(6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance',
                [$latitude, $longitude, $latitude]
            )
            ->having('distance', '<=', $radiusInKm)
            ->orderBy('distance')
            ->get();
    }
    
    /**
     * Récupère les disponibilités filtrées par préférences utilisateur.
     *
     * @param int $userId
     * @param array|null $userPreferences
     * @param array $filters
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function findByUserPreferences(int $userId, ?array $userPreferences, array $filters = []): \Illuminate\Database\Eloquent\Collection
    {
        try {
            // Récupérer les disponibilités de base (exclure celles de l'utilisateur actuel)
            $availabilitiesQuery = $this->availability->with(['user', 'user.profile'])
                ->where('user_id', '!=', $userId)
                ->where('is_active', true)
                ->orderBy('created_at', 'desc');
            
            // Appliquer les filtres basés sur les préférences utilisateur
            if ($userPreferences) {
                // Filtre par genre si défini dans les préférences
                if (isset($userPreferences['gender_preference']) && $userPreferences['gender_preference'] !== 'both') {
                    $gender = $userPreferences['gender_preference'];
                    $availabilitiesQuery->whereHas('user.profile', function ($q) use ($gender) {
                        $q->where('gender', $gender);
                    });
                }
                
                // Filtre par tranche d'âge si définie dans les préférences
                if (isset($userPreferences['age_range_min']) && isset($userPreferences['age_range_max'])) {
                    $minAge = $userPreferences['age_range_min'];
                    $maxAge = $userPreferences['age_range_max'];
                    
                    $availabilitiesQuery->whereHas('user.profile', function ($q) use ($minAge, $maxAge) {
                        $q->whereRaw('TIMESTAMPDIFF(YEAR, birthdate, CURDATE()) >= ?', [$minAge])
                          ->whereRaw('TIMESTAMPDIFF(YEAR, birthdate, CURDATE()) <= ?', [$maxAge]);
                    });
                }
            }
            
            // Appliquer les filtres supplémentaires
            if (!empty($filters)) {
                // Filtre par cuisine
                if (isset($filters['cuisine']) && !empty($filters['cuisine'])) {
                    $cuisine = $filters['cuisine'];
                    $availabilitiesQuery->where(function ($q) use ($cuisine) {
                        $q->whereJsonContains('preferences->cuisine', $cuisine)
                          ->orWhereNull('preferences->cuisine');
                    });
                }
                
                // Filtre par mode de paiement
                if (isset($filters['payment']) && !empty($filters['payment'])) {
                    $payment = $filters['payment'];
                    $availabilitiesQuery->where(function ($q) use ($payment) {
                        $q->whereJsonContains('preferences->payment', $payment)
                          ->orWhereNull('preferences->payment');
                    });
                }
            }
            
            $availabilities = $availabilitiesQuery->get();
            
            // Nous avons besoin de conserver les IDs des disponibilités filtrées pour les récupérer ensuite
            $filteredIds = $availabilities->pluck('id')->toArray();
            
            // Filtrage supplémentaire pour les centres d'intérêt
            if ($userPreferences && isset($userPreferences['interests']) && !empty($userPreferences['interests'])) {
                $interests = $userPreferences['interests'];
                
                // Filtrer les disponibilités dont l'utilisateur a au moins un intérêt commun
                $filteredIds = $availabilities->filter(function ($availability) use ($interests) {
                    $userInterests = $availability->user->preferences['interests'] ?? [];
                    
                    // Vérifier s'il y a au moins un intérêt commun
                    return empty($userInterests) || count(array_intersect($interests, $userInterests)) > 0;
                })->pluck('id')->toArray();
            }
            
            // Récupérer les disponibilités filtrées sous forme d'Eloquent Collection
            $finalAvailabilities = $this->availability->whereIn('id', $filteredIds)->with(['user', 'user.profile'])->get();
            
            // Ajouter les scores de compatibilité
            foreach ($finalAvailabilities as $availability) {
                $compatibilityScore = $this->calculateCompatibilityScore($userId, $availability->user_id);
                $availability->compatibility = $compatibilityScore;
            }
            
            return $finalAvailabilities;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error finding availabilities by preferences', [
                'error' => $e->getMessage(),
                'user_id' => $userId,
            ]);
            
            // Retourner une collection Eloquent vide
            return $this->availability->newCollection();
        }
    }
    
    /**
     * Calcule le score de compatibilité entre deux utilisateurs basé sur leurs intérêts communs.
     *
     * @param int $userId1
     * @param int $userId2
     * @return int
     */
    private function calculateCompatibilityScore(int $userId1, int $userId2): int
    {
        $user1 = \App\Models\User::find($userId1);
        $user2 = \App\Models\User::find($userId2);
        
        if (!$user1 || !$user2) {
            return 0;
        }
        
        $user1Interests = $user1->preferences['interests'] ?? [];
        $user2Interests = $user2->preferences['interests'] ?? [];
        
        if (empty($user1Interests) || empty($user2Interests)) {
            return 0;
        }
        
        // Calculer le nombre d'intérêts communs
        $commonInterests = array_intersect($user1Interests, $user2Interests);
        $compatibilityScore = count($commonInterests) * 20; // 20% par intérêt commun
        
        return min(100, $compatibilityScore); // Maximum 100%
    }
}
