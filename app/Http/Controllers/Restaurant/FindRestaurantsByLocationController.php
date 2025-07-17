<?php

declare(strict_types=1);

namespace App\Http\Controllers\Restaurant;

use App\CQRS\Queries\FindRestaurantsByLocationQuery;
use App\CQRS\QueryHandlers\FindRestaurantsByLocationQueryHandler;

use App\Http\Requests\FindRestaurantsByLocationRequest;
use Illuminate\Http\JsonResponse;

final readonly class FindRestaurantsByLocationController
{
    /**
     * FindRestaurantsByLocationController constructor.
     *
     * @param FindRestaurantsByLocationQueryHandler $handler
     */
    public function __construct(
        private FindRestaurantsByLocationQueryHandler $handler
    ) {
    }

    /**
     * Handle the incoming request.
     *
     * @param FindRestaurantsByLocationRequest $request
     * @return JsonResponse
     */
    public function __invoke(FindRestaurantsByLocationRequest $request): JsonResponse
    {
        // Récupérer les données validées
        $validated = $request->validated();
        
        // Créer la requête
        $query = new FindRestaurantsByLocationQuery(
            $validated['latitude'],
            $validated['longitude'],
            $validated['radius'],
            $validated['cuisine_types'] ?? null,
            $validated['price_ranges'] ?? null,
            $validated['limit'] ?? 20,
            $validated['offset'] ?? 0
        );
        
        // Exécuter la requête
        $restaurants = $this->handler->handle($query);
        
        // Retourner la réponse
        return response()->json([
            'message' => 'Restaurants found successfully',
            'data' => $restaurants
        ]);
    }
}
