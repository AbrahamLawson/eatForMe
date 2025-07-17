<?php

declare(strict_types=1);

namespace App\Http\Controllers\Message;

use App\CQRS\Queries\GetConversationQuery;
use App\CQRS\QueryHandlers\GetConversationQueryHandler;

use App\Http\Requests\GetConversationRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

final readonly class GetConversationController
{
    /**
     * GetConversationController constructor.
     *
     * @param GetConversationQueryHandler $handler
     */
    public function __construct(
        private GetConversationQueryHandler $handler
    ) {
    }

    /**
     * Handle the incoming request.
     *
     * @param GetConversationRequest $request
     * @return JsonResponse
     */
    public function __invoke(GetConversationRequest $request): JsonResponse
    {
        // Récupérer les données validées
        $validated = $request->validated();
        
        // Définir les valeurs par défaut pour la pagination
        $page = $validated['page'] ?? 1;
        $perPage = $validated['per_page'] ?? 15;
        
        // Créer la requête
        $query = new GetConversationQuery(
            userId: Auth::id(),
            matchId: $validated['match_id'],
            page: $page,
            perPage: $perPage
        );
        
        // Exécuter la requête
        $messages = $this->handler->handle($query);
        
        // Retourner la réponse
        return response()->json([
            'success' => true,
            'data' => [
                'messages' => $messages,
                'pagination' => [
                    'current_page' => $page,
                    'per_page' => $perPage,
                    'total' => count($messages) // Idéalement, cela devrait être le nombre total de messages
                ]
            ]
        ]);
    }
}
