<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use App\Models\Availability;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

final class GeolocationService
{
    /**
     * Calcule la distance entre deux points géographiques en utilisant la formule de Haversine.
     * 
     * @param float $lat1 Latitude du premier point
     * @param float $lon1 Longitude du premier point
     * @param float $lat2 Latitude du deuxième point
     * @param float $lon2 Longitude du deuxième point
     * @return float Distance en kilomètres
     */
    public function calculateDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        // Rayon de la Terre en kilomètres
        $earthRadius = 6371;
        
        // Conversion des degrés en radians
        $lat1 = deg2rad($lat1);
        $lon1 = deg2rad($lon1);
        $lat2 = deg2rad($lat2);
        $lon2 = deg2rad($lon2);
        
        // Formule de Haversine
        $latDelta = $lat2 - $lat1;
        $lonDelta = $lon2 - $lon1;
        $a = sin($latDelta/2) * sin($latDelta/2) + cos($lat1) * cos($lat2) * sin($lonDelta/2) * sin($lonDelta/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        
        return $earthRadius * $c;
    }
    
    /**
     * Génère une expression SQL pour calculer la distance avec la formule de Haversine.
     *
     * @param float $latitude Latitude du point central
     * @param float $longitude Longitude du point central
     * @return string Expression SQL Haversine
     */
    public function getHaversineFormula(float $latitude, float $longitude): string
    {
        return "(
            6371 * acos(
                cos(radians($latitude)) * 
                cos(radians(profiles.latitude)) * 
                cos(radians(profiles.longitude) - radians($longitude)) + 
                sin(radians($latitude)) * 
                sin(radians(profiles.latitude))
            )
        )";
    }

    /**
     * Trouve les utilisateurs à proximité en fonction des critères de recherche.
     *
     * @param User $currentUser L'utilisateur actuel
     * @param float $latitude La latitude de la position de l'utilisateur
     * @param float $longitude La longitude de la position de l'utilisateur
     * @param float $distance La distance maximale de recherche en kilomètres
     * @param string $activity Le type d'activité recherchée (eat, drink, chat)
     * @param string $profile Le type de profil recherché (any, male, female)
     * @return array
     */
    public function findNearbyUsers(
        User $currentUser,
        float $latitude,
        float $longitude,
        float $distance,
        string $activity,
        string $profile = 'any'
    ): array {
        // Utiliser la formule de Haversine pour calculer la distance
        $haversine = $this->getHaversineFormula($latitude, $longitude);
        
        // Utiliser une requête SQL brute pour de meilleures performances
        $query = DB::table('users')
            ->select([
                'users.id',
                'users.name',
                'users.email',
                'profiles.latitude',
                'profiles.longitude',
                DB::raw("$haversine AS distance")
            ])
            ->join('profiles', 'users.id', '=', 'profiles.user_id') // Joindre la table profiles pour accéder aux coordonnées
            ->join('availabilities', 'users.id', '=', 'availabilities.user_id')
            ->where('users.id', '!=', $currentUser->id) // Exclure l'utilisateur actuel
            ->whereNotNull('profiles.latitude') // S'assurer que les utilisateurs ont des coordonnées dans leur profil
            ->whereNotNull('profiles.longitude')
            ->whereRaw("$haversine <= ?", [$distance]) // Filtrer par distance
            ->whereRaw("JSON_EXTRACT(availabilities.preferences, '$.activity') = ?", [$activity]) // Filtrer par activité dans les préférences JSON
            ->where('availabilities.is_active', true) // Seulement les disponibilités actives
            ->orderBy('distance', 'asc'); // Trier par distance croissante
        
        // Note: Le filtrage par profil spécifique est désactivé car la colonne 'gender' n'existe pas
        // dans la table users. Si nécessaire, implémenter un filtrage basé sur les préférences JSON.
        
        // Exécuter la requête
        $nearbyUsers = $query->get();
        
        // Récupérer les IDs des utilisateurs trouvés
        $userIds = $nearbyUsers->pluck('id')->toArray();
        
        // Si aucun utilisateur n'est trouvé, retourner un tableau vide
        if (empty($userIds)) {
            return [];
        }
        
        // Récupérer les utilisateurs avec leurs disponibilités en utilisant Eloquent
        $users = User::with(['availabilities' => function ($query) use ($activity) {
            $query->where('preferences->activity', $activity)
                  ->where('is_active', true)
                  ->orderBy('created_at', 'desc');
        }])
        ->whereIn('id', $userIds)
        ->get()
        ->map(function ($user) use ($nearbyUsers) {
            // Ajouter la distance calculée à l'objet utilisateur
            $userWithDistance = $nearbyUsers->firstWhere('id', $user->id);
            $user->distance = $userWithDistance->distance;
            return $user;
        })
        ->sortBy('distance');
        
        // Formater les résultats
        return $this->formatNearbyUsersResponse($users);
    }
    
    /**
     * Formate la réponse pour les utilisateurs à proximité.
     *
     * @param Collection $users
     * @return array
     */
    private function formatNearbyUsersResponse(Collection $users): array
    {
        $result = [];
        
        foreach ($users as $user) {
            $availabilities = [];
            
            foreach ($user->availabilities as $availability) {
                $availabilities[] = [
                    'id' => $availability->id,
                    'activity_type' => $availability->preferences['activity'] ?? 'unknown',
                    'is_active' => $availability->is_active,
                    'latitude' => $availability->latitude,
                    'longitude' => $availability->longitude,
                    'preferences' => $availability->preferences,
                    'created_at' => $availability->created_at->format('Y-m-d H:i:s'),
                ];
            }
            
            $result[] = [
                'id' => $user->id,
                'name' => $user->name,
                'profile_photo_url' => $user->profile_photo_url ?? null,
                'gender' => $user->preferences['gender'] ?? 'unknown',
                'bio' => $user->profile->bio ?? null,
                'distance' => round($user->distance, 2), // Arrondir à 2 décimales
                'latitude' => $user->profile->latitude ?? null,
                'longitude' => $user->profile->longitude ?? null,
                'availabilities' => $availabilities,
            ];
        }
        
        return $result;
    }
}
