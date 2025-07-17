<?php

declare(strict_types=1);

namespace App\Http\Controllers\Match;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Availability;
use App\Services\MatchingService;
use App\Services\GeolocationService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TestMatchingController extends Controller
{
    /**
     * TestMatchingController constructor.
     *
     * @param MatchingService $matchingService
     * @param GeolocationService $geolocationService
     */
    public function __construct(
        private MatchingService $matchingService,
        private GeolocationService $geolocationService
    ) {
    }

    /**
     * Affiche la page de test de matching
     *
     * @return Response
     */
    public function index(): Response
    {
        // Récupérer tous les utilisateurs pour le test
        $users = User::with('profile')->get()->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'profile_photo_url' => $user->profile_photo_url,
                'has_availability' => $user->availabilities()->where('is_active', true)->exists(),
                'latitude' => $user->profile->latitude ?? null,
                'longitude' => $user->profile->longitude ?? null,
            ];
        });

        return Inertia::render('Match/TestMatching', [
            'users' => $users
        ]);
    }

    /**
     * Teste le matching entre deux utilisateurs
     *
     * @param Request $request
     * @return Response
     */
    public function testMatching(Request $request): Response
    {
        $validated = $request->validate([
            'user1_id' => 'required|exists:users,id',
            'user2_id' => 'required|exists:users,id|different:user1_id',
            'activity1' => 'required|in:eat,drink,chat',
            'activity2' => 'required|in:eat,drink,chat',
            'distance1' => 'required|numeric|min:0.1|max:500',
            'distance2' => 'required|numeric|min:0.1|max:500'
        ]);

        $user1 = User::with(['profile', 'availabilities'])->findOrFail($validated['user1_id']);
        $user2 = User::with(['profile', 'availabilities'])->findOrFail($validated['user2_id']);

        // Vérifier si les deux utilisateurs ont des disponibilités actives pour leurs activités respectives
        $user1HasActiveAvailability = $this->ensureUserHasActiveAvailability($user1, $validated['activity1']);
        $user2HasActiveAvailability = $this->ensureUserHasActiveAvailability($user2, $validated['activity2']);

        if (!$user1HasActiveAvailability || !$user2HasActiveAvailability) {
            return Inertia::render('Match/TestMatching', [
                'users' => User::with('profile')->get()->map(function ($user) {
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'profile_photo_url' => $user->profile_photo_url,
                        'has_availability' => $user->availabilities()->where('is_active', true)->exists(),
                        'latitude' => $user->profile->latitude ?? null,
                        'longitude' => $user->profile->longitude ?? null,
                    ];
                }),
                'error' => !$user1HasActiveAvailability 
                    ? "L'utilisateur 1 n'a pas de disponibilité active pour l'activité " . $validated['activity1'] 
                    : "L'utilisateur 2 n'a pas de disponibilité active pour l'activité " . $validated['activity2']
            ]);
        }

        // Calculer la distance entre les deux utilisateurs
        $distance = $this->calculateDistance($user1, $user2);
        
        // Vérifier si les utilisateurs sont dans la portée de distance de chacun
        // Ajout d'une marge de tolérance de 0.5 km pour éviter les problèmes d'arrondi
        $tolerance = 0.5;
        $user1InRange = $distance <= ($validated['distance1'] + $tolerance);
        $user2InRange = $distance <= ($validated['distance2'] + $tolerance);
        
        // Les deux utilisateurs doivent respecter leurs distances maximales respectives
        // C'est-à-dire que la distance réelle doit être inférieure ou égale à la distance maximale définie par chaque utilisateur
        $inRange = $user1InRange && $user2InRange;
        
        // Vérifier si les activités sont compatibles
        $activitiesMatch = $validated['activity1'] === $validated['activity2'];

        // Formater les données pour le service de matching
        $nearbyUser = [
            'id' => $user2->id,
            'name' => $user2->name,
            'profile_photo_url' => $user2->profile_photo_url,
            'gender' => $user2->preferences['gender'] ?? 'unknown',
            'age' => $user2->preferences['age'] ?? 30,
            'distance' => $distance,
            'activity' => $validated['activity2'],
            'compatibility_score' => 0
        ];

        // Calculer le score de compatibilité avec détails
        $compatibilityScore = 0;
        $scoreDetails = [];
        if ($activitiesMatch) {
            $user1Preferences = $user1->preferences ?? [];
            $user2Preferences = $user2->preferences ?? [];
            $scoreDetails = $this->matchingService->calculateCompatibilityScore(
                $user1Preferences, 
                $user2Preferences, 
                $validated['activity1'], 
                $distance,
                true // Demander les détails du calcul
            );
            $compatibilityScore = $scoreDetails['final_score'] ?? 0;
        }

        $nearbyUser['compatibility_score'] = $compatibilityScore;

        // Déterminer si les utilisateurs sont compatibles
        // On accepte n'importe quel score si les utilisateurs sont dans la portée et ont la même activité
        $isCompatible = $inRange && $activitiesMatch;
        
        // Le score de compatibilité est utilisé pour classer les matchs potentiels
        // mais n'est pas un critère d'exclusion

        // Créer un match si les utilisateurs sont compatibles
        $matchCreated = false;
        $matchDetails = null;

        if (!empty($potentialMatches)) {
            $match = $potentialMatches[0];
            try {
                $userAvailability = $user1->availabilities->where('is_active', true)->first();
                $matchObject = $this->matchingService->createMatch(
                    $user1->id,
                    $user2->id,
                    $userAvailability->id
                );
                $matchCreated = true;
                $matchDetails = [
                    'id' => $matchObject->id,
                    'status' => $matchObject->status,
                    'created_at' => $matchObject->created_at->format('Y-m-d H:i:s')
                ];
            } catch (\Exception $e) {
                $matchCreated = false;
            }
        }

        // Renvoyer les résultats du test
        return Inertia::render('Match/TestMatching', [
            'users' => User::with('profile')->get()->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'profile_photo_url' => $user->profile_photo_url,
                    'has_availability' => $user->availabilities()->where('is_active', true)->exists(),
                    'latitude' => $user->profile->latitude ?? null,
                    'longitude' => $user->profile->longitude ?? null,
                ];
            }),
            'testResults' => [
                'user1' => [
                    'id' => $user1->id,
                    'name' => $user1->name,
                    'profile_photo_url' => $user1->profile_photo_url,
                    'activity' => $validated['activity1'],
                    'distance' => $validated['distance1'],
                    'inRange' => $user1InRange,
                ],
                'user2' => [
                    'id' => $user2->id,
                    'name' => $user2->name,
                    'profile_photo_url' => $user2->profile_photo_url,
                    'activity' => $validated['activity2'],
                    'distance' => $validated['distance2'],
                    'inRange' => $user2InRange,
                ],
                'activitiesMatch' => $activitiesMatch,
                'distance' => $distance,
                'isCompatible' => $isCompatible,
                'compatibilityScore' => $compatibilityScore,
                'scoreDetails' => $scoreDetails,
                'matchCreated' => $matchCreated,
                'matchDetails' => $matchDetails
            ]
        ]);
    }

    /**
     * S'assure qu'un utilisateur a une disponibilité active pour l'activité spécifiée
     * Si non, en crée une
     *
     * @param User $user
     * @param string $activity
     * @return bool
     */
    private function ensureUserHasActiveAvailability(User $user, string $activity): bool
    {
        // Vérifier si l'utilisateur a déjà une disponibilité active
        $availability = $user->availabilities()->where('is_active', true)->first();

        if ($availability) {
            // Mettre à jour les préférences pour inclure l'activité spécifiée
            $preferences = $availability->preferences ?? [];
            $preferences['activity'] = $activity;
            $availability->preferences = $preferences;
            $availability->save();
            return true;
        }

        // Si l'utilisateur n'a pas de coordonnées dans son profil, impossible de créer une disponibilité
        if (!$user->profile || !$user->profile->latitude || !$user->profile->longitude) {
            return false;
        }

        // Créer une nouvelle disponibilité
        $availability = new Availability();
        $availability->user_id = $user->id;
        $availability->is_active = true;
        $availability->latitude = $user->profile->latitude;
        $availability->longitude = $user->profile->longitude;
        $availability->preferences = [
            'activity' => $activity
        ];
        $availability->save();

        return true;
    }

    /**
     * Met à jour la disponibilité de l'utilisateur connecté
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateAvailability(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'is_active' => 'required|boolean',
            'activity' => 'required|string|in:eat,drink,chat',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);
        
        $user = User::findOrFail($validated['user_id']);
        
        // Chercher une disponibilité existante pour cette activité
        $availability = $user->availabilities()
            ->whereJsonContains('preferences->activity', $validated['activity'])
            ->first();
        
        // Si aucune disponibilité n'existe pour cette activité, en créer une nouvelle
        if (!$availability) {
            $availability = new Availability([
                'user_id' => $user->id,
                'is_active' => $validated['is_active'],
                'latitude' => $validated['latitude'],
                'longitude' => $validated['longitude'],
                'preferences' => [
                    'activity' => $validated['activity'],
                    'gender' => $user->preferences['gender'] ?? 'any',
                ],
            ]);
        } else {
            // Mettre à jour la disponibilité existante
            $availability->is_active = $validated['is_active'];
            $availability->latitude = $validated['latitude'];
            $availability->longitude = $validated['longitude'];
            
            // Mettre à jour les préférences tout en conservant les autres valeurs
            $preferences = $availability->preferences;
            $preferences['activity'] = $validated['activity'];
            $availability->preferences = $preferences;
        }
        
        $availability->save();
        
        return redirect()->back()->with('success', 'Disponibilité mise à jour avec succès');
    }

    /**
     * Calcule la distance entre deux utilisateurs en utilisant leurs coordonnées
     *
     * @param User $user1
     * @param User $user2
     * @return float
     */
    private function calculateDistance(User $user1, User $user2): float
    {
        if (!$user1->profile || !$user2->profile || 
            !$user1->profile->latitude || !$user1->profile->longitude || 
            !$user2->profile->latitude || !$user2->profile->longitude) {
            return 999.99; // Distance par défaut si les coordonnées ne sont pas disponibles
        }

        return $this->geolocationService->calculateDistance(
            (float) $user1->profile->latitude,
            (float) $user1->profile->longitude,
            (float) $user2->profile->latitude,
            (float) $user2->profile->longitude
        );
    }
}
