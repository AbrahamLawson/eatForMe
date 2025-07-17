<?php

declare(strict_types=1);

namespace App\Http\Controllers\Match;

use App\CQRS\CommandHandlers\CreateMatchCommandHandler;
use App\CQRS\Commands\CreateMatchCommand;
use App\Http\Requests\CreateMatchRequest;
use Illuminate\Http\JsonResponse;

final readonly class CreateMatchController
{
    /**
     * CreateMatchController constructor.
     *
     * @param CreateMatchCommandHandler $handler
     */
    public function __construct(
        private CreateMatchCommandHandler $handler
    ) {
    }

    /**
     * Handle the incoming request.
     *
     * @param CreateMatchRequest $request
     * @return JsonResponse
     */
    public function __invoke(CreateMatchRequest $request): JsonResponse
    {
        // Récupérer les données validées
        $validated = $request->validated();
        
        // Créer la commande
        $command = new CreateMatchCommand(
            $request->user()->id,
            $validated['initiator_availability_id'],
            $validated['receiver_availability_id'],
            $validated['restaurant_id'],
            $validated['proposed_datetime'],
            $validated['message'] ?? null
        );
        
        // Exécuter la commande
        $match = $this->handler->handle($command);
        
        // Retourner la réponse
        return response()->json([
            'message' => 'Match created successfully',
            'data' => $match
        ], 201);
    }
}
