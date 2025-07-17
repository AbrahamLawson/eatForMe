<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use App\Repositories\Interfaces\AvailabilityRepositoryInterface;
use App\Repositories\Interfaces\UserMatchRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Models\UserMatch;
use Illuminate\Database\Eloquent\Collection;

final class MatchingService
{
    /**
     * MatchingService constructor.
     *
     * @param AvailabilityRepositoryInterface $availabilityRepository
     * @param UserRepositoryInterface $userRepository
     * @param UserMatchRepositoryInterface $userMatchRepository
     */
    public function __construct(
        private AvailabilityRepositoryInterface $availabilityRepository,
        private UserRepositoryInterface $userRepository,
        private UserMatchRepositoryInterface $userMatchRepository
    ) {}

    /**
     * Find potential matches for a user based on their availability.
     *
     * @param int $availabilityId
     * @param float $maxDistanceKm
     * @return Collection
     */
    public function findPotentialMatchesByAvailability(int $availabilityId, float $maxDistanceKm = 5.0): Collection
    {
        // Get the availability
        $availability = $this->availabilityRepository->findById($availabilityId);

        if (!$availability) {
            return collect();
        }

        // Find other availabilities near this location
        $nearbyAvailabilities = $this->availabilityRepository->findNearLocation(
            $availability->latitude,
            $availability->longitude,
            $maxDistanceKm
        )->filter(function ($nearbyAvailability) use ($availability) {
            // Exclude the user's own availability and only include active availabilities
            return $nearbyAvailability->user_id !== $availability->user_id &&
                   $nearbyAvailability->is_active === true;
        });

        return $nearbyAvailabilities;
    }

    /**
     * Trouve les meilleurs matchs potentiels pour un utilisateur à partir d'une liste d'utilisateurs à proximité.
     * Cette méthode est utilisée spécifiquement pour la recherche active.
     *
     * @param User $currentUser L'utilisateur actuel
     * @param array $nearbyUsers Liste des utilisateurs à proximité
     * @param string $activity Type d'activité recherchée
     * @return array Liste des meilleurs matchs potentiels avec score de compatibilité
     */
    /**
     * Trouve les meilleurs matchs potentiels pour un utilisateur à partir d'une liste d'utilisateurs à proximité.
     * Cette méthode est utilisée spécifiquement pour la recherche active.
     *
     * @param User $currentUser L'utilisateur actuel
     * @param array $nearbyUsers Liste des utilisateurs à proximité
     * @param string $activity Type d'activité recherchée
     * @param string $profileType Type de profil recherché (any, male, female)
     * @return array Liste des meilleurs matchs potentiels avec score de compatibilité
     */
    public function findPotentialMatches(User $currentUser, array $nearbyUsers, string $activity, string $profileType = 'any'): array
    {
        if (empty($nearbyUsers)) {
            return [];
        }

        // Récupérer les préférences de l'utilisateur actuel
        $userPreferences = $currentUser->preferences ?? null;

        // Récupérer la disponibilité active de l'utilisateur
        $userAvailability = $currentUser->availabilities
            ->where('is_active', true)
            ->first();

        // Vérifier si les préférences de disponibilité correspondent à l'activité recherchée
        if ($userAvailability && isset($userAvailability->preferences['activity'])) {
            if ($userAvailability->preferences['activity'] !== $activity) {
                $userAvailability = null; // La disponibilité ne correspond pas à l'activité recherchée
            }
        }

        if (!$userAvailability) {
            return [];
        }

        $potentialMatches = [];

        foreach ($nearbyUsers as $nearbyUser) {
            // Filtrer par type de profil si spécifié
            if ($profileType !== 'any') {
                $userGender = $nearbyUser['gender'] ?? 'unknown';
                // Si le profil ne correspond pas au type recherché, passer au suivant
                if ($userGender !== $profileType && $userGender !== 'unknown') {
                    continue;
                }
            }

            // Calculer un score de compatibilité
            $compatibilityScore = $this->calculateCompatibilityScore($currentUser, $nearbyUser, $userPreferences);

            // Vérifier les disponibilités compatibles
            $compatibleAvailability = null;
            foreach ($nearbyUser['availabilities'] as $availability) {
                // Vérifier si la disponibilité est active
                if ($availability['is_active']) {
                    // Vérifier si l'activité dans les préférences correspond à celle recherchée
                    if (isset($availability['preferences']['activity']) && $availability['preferences']['activity'] === $activity) {
                        $compatibleAvailability = $availability;
                        break;
                    }
                }
            }

            // Si une disponibilité compatible est trouvée et le score est suffisant, ajouter aux matchs potentiels
            if ($compatibleAvailability && $compatibilityScore >= 50) {
                $potentialMatches[] = [
                    'user' => $nearbyUser,
                    'compatibility_score' => $compatibilityScore,
                    'compatible_availability' => $compatibleAvailability,
                    'user_availability_id' => $userAvailability->id
                ];
            }
        }

        // Trier par score de compatibilité décroissant
        usort($potentialMatches, function ($a, $b) {
            return $b['compatibility_score'] <=> $a['compatibility_score'];
        });

        // Limiter à 5 matchs maximum
        return array_slice($potentialMatches, 0, 5);
    }

    /**
     * Calcule un score de compatibilité entre deux utilisateurs.
     *
     * @param array|null $user1Preferences Préférences du premier utilisateur
     * @param array|null $user2Preferences Préférences du deuxième utilisateur
     * @param string|null $activity Activité commune
     * @param float $distance Distance entre les utilisateurs
     * @return int Score de compatibilité (0-100)
     */
    public function calculateCompatibilityScore(?array $user1Preferences, ?array $user2Preferences, ?string $activity = null, float $distance = 0, bool $returnDetails = false): array|int
    {
        // Score de base
        $score = 50;

        // Récupérer les préférences des deux utilisateurs
        $user1Prefs = $user1Preferences ?? [];
        $user2Prefs = $user2Preferences ?? [];
        
        // Préparer un tableau pour stocker les détails du calcul si demandé
        $scoreDetails = [];

        // 1. Compatibilité des préférences de rencontre (homme, femme, les deux)
        // Cette partie est cruciale et vaut jusqu'à 30 points
        $genderScore = $this->calculateGenderCompatibilityScore($user1Prefs, $user2Prefs);
        $score += $genderScore;
        $scoreDetails['gender_score'] = $genderScore;

        // 2. Compatibilité de tranche d'âge
        // Cette partie est importante et vaut jusqu'à 20 points
        $ageScore = $this->calculateAgeCompatibilityScore($user1Prefs, $user2Prefs);
        $score += $ageScore;
        $scoreDetails['age_score'] = $ageScore;

        // 3. Compatibilité des centres d'intérêt
        // Cette partie vaut jusqu'à 15 points
        $interestsScore = $this->calculateInterestsCompatibilityScore($user1Prefs, $user2Prefs);
        $score += $interestsScore;
        $scoreDetails['interests_score'] = $interestsScore;
        
        // 4. Compatibilité des préférences de paiement
        // Cette partie vaut jusqu'à 10 points
        $paymentScore = $this->calculatePaymentPreferenceCompatibilityScore($user1Prefs, $user2Prefs);
        $score += $paymentScore;
        $scoreDetails['payment_score'] = $paymentScore;

        // 5. Bonus pour l'activité spécifique choisie
        // Cette partie vaut jusqu'à 15 points
        $activityScore = 0;
        if ($activity !== null) {
            $activityScore = 15; // Bonus important pour avoir choisi la même activité
            $score += $activityScore;
        }
        $scoreDetails['activity_score'] = $activityScore;
        
        // 6. Ajustement basé sur la distance
        // Pénalité proportionnelle à la distance (jusqu'à -10 points)
        $distanceScore = 0;
        if ($distance > 0) {
            // Considérons que la distance idéale est inférieure à 2km
            // Au-delà, on commence à pénaliser progressivement
            if ($distance <= 2) {
                // Pas de pénalité pour les distances très proches
                $distanceScore = 5;
                $score += $distanceScore;
            } else {
                // Pénalité progressive basée sur la distance
                // -1 point par kilomètre au-delà de 2km, jusqu'à -10 points maximum
                $distancePenalty = min(10, max(0, ($distance - 2)));
                $distanceScore = -$distancePenalty;
                $score += $distanceScore;
            }
        }
        $scoreDetails['distance_score'] = $distanceScore;
        
        // Score de base
        $scoreDetails['base_score'] = 50;
        
        // Score final (limité entre 0 et 100)
        $finalScore = max(0, min(100, $score));
        $scoreDetails['final_score'] = $finalScore;

        // Retourner soit le score final, soit les détails du calcul
        if ($returnDetails) {
            return $scoreDetails;
        }
        
        return $finalScore;
    }

    /**
     * Calcule le score de compatibilité basé sur les préférences de genre.
     *
     * @param array $user1Prefs
     * @param array $user2Prefs
     * @return int
     */
    private function calculateGenderCompatibilityScore(array $user1Prefs, array $user2Prefs): int
    {
        $score = 0;
        
        // Vérifier si les préférences de genre sont compatibles
        if (isset($user1Prefs['gender_preference']) && isset($user2Prefs['gender'])) {
            $user1GenderPref = $user1Prefs['gender_preference'];
            $user2Gender = $user2Prefs['gender'];
            
            // Si l'utilisateur 1 préfère le genre de l'utilisateur 2
            if ($user1GenderPref === 'any' || $user1GenderPref === $user2Gender) {
                $score += 15;
            } else {
                $score -= 30; // Forte pénalité si incompatible
            }
        }
        
        // Vérifier dans l'autre sens aussi
        if (isset($user2Prefs['gender_preference']) && isset($user1Prefs['gender'])) {
            $user2GenderPref = $user2Prefs['gender_preference'];
            $user1Gender = $user1Prefs['gender'];
            
            // Si l'utilisateur 2 préfère le genre de l'utilisateur 1
            if ($user2GenderPref === 'any' || $user2GenderPref === $user1Gender) {
                $score += 15;
            } else {
                $score -= 30; // Forte pénalité si incompatible
            }
        }
        
        return $score;
    }

    /**
     * Calcule le score de compatibilité basé sur les tranches d'âge.
     *
     * @param array $user1Prefs
     * @param array $user2Prefs
     * @return int
     */
    private function calculateAgeCompatibilityScore(array $user1Prefs, array $user2Prefs): int
    {
        $score = 0;
        
        // Vérifier si les tranches d'âge sont compatibles
        if (isset($user1Prefs['age_range_min']) && isset($user1Prefs['age_range_max']) && 
            isset($user2Prefs['age'])) {
            
            $user2Age = $user2Prefs['age'];
            
            // Vérifier si l'utilisateur 2 est dans la tranche d'âge préférée de l'utilisateur 1
            if ($user2Age >= $user1Prefs['age_range_min'] && $user2Age <= $user1Prefs['age_range_max']) {
                $score += 10;
            } else {
                // Calculer la distance à la tranche d'âge
                $distanceToRange = min(
                    abs($user2Age - $user1Prefs['age_range_min']),
                    abs($user2Age - $user1Prefs['age_range_max'])
                );
                
                // Pénalité proportionnelle à la distance
                $score -= min(10, $distanceToRange);
            }
        }
        
        // Vérifier dans l'autre sens aussi
        if (isset($user2Prefs['age_range_min']) && isset($user2Prefs['age_range_max']) && 
            isset($user1Prefs['age'])) {
            
            $user1Age = $user1Prefs['age'];
            
            // Vérifier si l'utilisateur 1 est dans la tranche d'âge préférée de l'utilisateur 2
            if ($user1Age >= $user2Prefs['age_range_min'] && $user1Age <= $user2Prefs['age_range_max']) {
                $score += 10;
            } else {
                // Calculer la distance à la tranche d'âge
                $distanceToRange = min(
                    abs($user1Age - $user2Prefs['age_range_min']),
                    abs($user1Age - $user2Prefs['age_range_max'])
                );
                
                // Pénalité proportionnelle à la distance
                $score -= min(10, $distanceToRange);
            }
        }
        
        return $score;
    }

    /**
     * Calcule le score de compatibilité basé sur les centres d'intérêt.
     *
     * @param array $user1Prefs
     * @param array $user2Prefs
     * @return int
     */
    private function calculateInterestsCompatibilityScore(array $user1Prefs, array $user2Prefs): int
    {
        $score = 0;
        
        // Vérifier si les utilisateurs ont des centres d'intérêt en commun
        if (isset($user1Prefs['interests']) && isset($user2Prefs['interests'])) {
            $user1Interests = is_array($user1Prefs['interests']) ? $user1Prefs['interests'] : [$user1Prefs['interests']];
            $user2Interests = is_array($user2Prefs['interests']) ? $user2Prefs['interests'] : [$user2Prefs['interests']];
            
            // Trouver les intérêts communs
            $commonInterests = array_intersect($user1Interests, $user2Interests);
            $commonCount = count($commonInterests);
            
            // Attribuer des points en fonction du nombre d'intérêts communs
            // 3 points par intérêt commun, maximum 15 points
            $score += min(15, $commonCount * 3);
        }
        
        return $score;
    }
    
    /**
     * Calcule le score de compatibilité basé sur les préférences de paiement.
     *
     * @param array $user1Prefs
     * @param array $user2Prefs
     * @return int
     */
    private function calculatePaymentPreferenceCompatibilityScore(array $user1Prefs, array $user2Prefs): int
    {
        $score = 0;
        
        // Vérifier si les utilisateurs ont des préférences de paiement
        if (isset($user1Prefs['paymentPreference']) && isset($user2Prefs['paymentPreference'])) {
            $user1PaymentPref = $user1Prefs['paymentPreference'];
            $user2PaymentPref = $user2Prefs['paymentPreference'];
            
            // Si les deux utilisateurs n'ont pas de préférence ("peu importe")
            if ($user1PaymentPref === null || $user1PaymentPref === 'any') {
                $score += 5; // Score moyen car flexible
            }
            // Si les préférences sont complémentaires
            elseif (
                ($user1PaymentPref === 'i_pay' && $user2PaymentPref === 'they_pay') ||
                ($user1PaymentPref === 'they_pay' && $user2PaymentPref === 'i_pay')
            ) {
                $score += 10; // Score maximum car préférences parfaitement complémentaires
            }
            // Si les deux veulent payer
            elseif ($user1PaymentPref === 'i_pay' && $user2PaymentPref === 'i_pay') {
                $score += 8; // Bon score car les deux sont généreux
            }
            // Si les deux veulent que l'autre paie
            elseif ($user1PaymentPref === 'they_pay' && $user2PaymentPref === 'they_pay') {
                $score -= 5; // Pénalité car conflit potentiel
            }
            // Si l'un veut partager et l'autre a une préférence spécifique
            elseif (
                ($user1PaymentPref === 'split' && ($user2PaymentPref === 'i_pay' || $user2PaymentPref === 'they_pay')) ||
                ($user2PaymentPref === 'split' && ($user1PaymentPref === 'i_pay' || $user1PaymentPref === 'they_pay'))
            ) {
                $score += 3; // Score moyen car compromis possible
            }
            // Si les deux veulent partager
            elseif ($user1PaymentPref === 'split' && $user2PaymentPref === 'split') {
                $score += 7; // Bon score car accord parfait sur le partage
            }
        } else if (isset($user1Prefs['paymentPreference']) || isset($user2Prefs['paymentPreference'])) {
            // Si un seul utilisateur a une préférence, score neutre
            $score += 2;
        }
        
        return $score;
    }

    /**
     * Create a match between two users.
     *
     * @param int $userId
     * @param int $matchedUserId
     * @param int $availabilityId
     * @return UserMatch
     */
    public function createMatch(int $userId, int $matchedUserId, int $availabilityId): UserMatch
    {
        // Nous supposons que $availabilityId est l'initiateur et nous devons trouver
        // l'ID de disponibilité du destinataire
        $receiverAvailability = $this->availabilityRepository->findByUserId($matchedUserId)->first();

        if (!$receiverAvailability) {
            throw new \InvalidArgumentException('No availability found for matched user');
        }

        // Créer le match en utilisant la nouvelle méthode
        $matchId = $this->userMatchRepository->createMatch($availabilityId, $receiverAvailability->id);

        // Récupérer et retourner l'objet match créé
        return $this->userMatchRepository->findById($matchId);
    }

    /**
     * Update the status of a match.
     *
     * @param int $matchId
     * @param string $status
     * @return UserMatch
     */
    public function updateMatchStatus(int $matchId, string $status): UserMatch
    {
        $match = $this->userMatchRepository->findById($matchId);

        if (!$match) {
            throw new \InvalidArgumentException("Match not found");
        }

        $data = [
            'status' => $status,
            'response_at' => now(),
        ];

        return $this->userMatchRepository->update($matchId, $data);
    }
}
