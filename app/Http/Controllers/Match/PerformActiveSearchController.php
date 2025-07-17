<?php

declare(strict_types=1);

namespace App\Http\Controllers\Match;

use App\CQRS\Queries\FindNearbyUsersQuery;
use App\CQRS\QueryHandlers\FindNearbyUsersQueryHandler;
use App\Http\Requests\ActiveSearchRequest;
use Inertia\Inertia;
use Inertia\Response;

final readonly class PerformActiveSearchController
{
    /**
     * PerformActiveSearchController constructor.
     *
     * @param FindNearbyUsersQueryHandler $handler
     */
    public function __construct(
        private FindNearbyUsersQueryHandler $handler
    ) {
    }

    /**
     * Recherche des utilisateurs à proximité en fonction des critères.
     *
     * @param ActiveSearchRequest $request
     * @return Response
     */
    public function __invoke(ActiveSearchRequest $request): Response
    {
        // Récupérer les données validées
        $validated = $request->validated();
        
        // Créer la requête
        $query = new FindNearbyUsersQuery(
            $request->user()->id,
            (float) $validated['latitude'],
            (float) $validated['longitude'],
            (float) $validated['distance'],
            $validated['activity'],
            $validated['profile'] ?? 'any'
        );
        
        // Exécuter la requête
        $nearbyUsers = $this->handler->handle($query);
        
        // Extraire les utilisateurs et les matchs potentiels
        $users = $nearbyUsers['users'] ?? [];
        $potentialMatches = $nearbyUsers['potential_matches'] ?? [];
        
        // Retourner la réponse Inertia avec les données dans le format attendu par le frontend
        return Inertia::render('ActiveSearch/Index', [
            'data' => $users,
            'potentialMatches' => $potentialMatches
        ]);
    }
}
