<?php

declare(strict_types=1);

namespace App\Http\Controllers\Profile;

use App\CQRS\Queries\GetProfilesByPreferencesQuery;
use App\CQRS\QueryHandlers\GetProfilesByPreferencesQueryHandler;

use App\Http\Requests\GetProfilesByPreferencesRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

final readonly class GetProfilesByPreferencesController
{
    /**
     * GetProfilesByPreferencesController constructor.
     *
     * @param GetProfilesByPreferencesQueryHandler $handler
     */
    public function __construct(
        private GetProfilesByPreferencesQueryHandler $handler
    ) {
    }

    /**
     * Handle the incoming request.
     *
     * @param GetProfilesByPreferencesRequest $request
     * @return JsonResponse
     */
    public function __invoke(GetProfilesByPreferencesRequest $request): JsonResponse
    {
        // Récupérer les données validées
        $validated = $request->validated();
        
        // Définir les valeurs par défaut pour la pagination
        $page = $validated['page'] ?? 1;
        $perPage = $validated['per_page'] ?? 15;
        
        // Créer la requête
        $query = new GetProfilesByPreferencesQuery(
            preferences: $validated['preferences'] ?? [],
            dietaryRestrictions: $validated['dietary_restrictions'] ?? [],
            favoriteCuisines: $validated['favorite_cuisines'] ?? [],
            maxDistance: $validated['max_distance'] ?? null,
            latitude: $validated['latitude'] ?? null,
            longitude: $validated['longitude'] ?? null,
            excludeUserId: Auth::id(),
            page: $page,
            perPage: $perPage
        );
        
        // Exécuter la requête
        $profiles = $this->handler->handle($query);
        
        // Retourner la réponse
        return response()->json([
            'success' => true,
            'data' => [
                'profiles' => $profiles,
                'pagination' => [
                    'current_page' => $page,
                    'per_page' => $perPage,
                    'total' => count($profiles) // Idéalement, cela devrait être le nombre total de profils
                ]
            ]
        ]);
    }
}
