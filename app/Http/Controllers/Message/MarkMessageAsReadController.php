<?php

declare(strict_types=1);

namespace App\Http\Controllers\Message;

use App\CQRS\CommandHandlers\MarkMessageAsReadCommandHandler;
use App\CQRS\Commands\MarkMessageAsReadCommand;

use App\Http\Requests\MarkMessageAsReadRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

final readonly class MarkMessageAsReadController
{
    /**
     * MarkMessageAsReadController constructor.
     *
     * @param MarkMessageAsReadCommandHandler $handler
     */
    public function __construct(
        private MarkMessageAsReadCommandHandler $handler
    ) {
    }

    /**
     * Handle the incoming request.
     *
     * @param MarkMessageAsReadRequest $request
     * @return JsonResponse
     */
    public function __invoke(MarkMessageAsReadRequest $request): JsonResponse
    {
        // Récupérer les données validées
        $validated = $request->validated();
        
        // Créer la commande
        $command = new MarkMessageAsReadCommand(
            messageId: $validated['message_id'],
            userId: Auth::id()
        );
        
        // Exécuter la commande
        $this->handler->handle($command);
        
        // Retourner la réponse
        return response()->json([
            'success' => true,
            'message' => 'Message marqué comme lu avec succès'
        ]);
    }
}
