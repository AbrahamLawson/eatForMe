<?php

declare(strict_types=1);

namespace App\Http\Controllers\Message;

use App\CQRS\CommandHandlers\SendMessageCommandHandler;
use App\CQRS\Commands\SendMessageCommand;

use App\Http\Requests\SendMessageRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

final readonly class SendMessageController
{
    /**
     * SendMessageController constructor.
     *
     * @param SendMessageCommandHandler $handler
     */
    public function __construct(
        private SendMessageCommandHandler $handler
    ) {
    }

    /**
     * Handle the incoming request.
     *
     * @param SendMessageRequest $request
     * @return JsonResponse
     */
    public function __invoke(SendMessageRequest $request): JsonResponse
    {
        // Récupérer les données validées
        $validated = $request->validated();
        
        // Créer la commande
        $command = new SendMessageCommand(
            senderId: Auth::id(),
            receiverId: $validated['receiver_id'],
            matchId: $validated['match_id'],
            content: $validated['content']
        );
        
        // Exécuter la commande
        $messageId = $this->handler->handle($command);
        
        // Retourner la réponse
        return response()->json([
            'success' => true,
            'message' => 'Message envoyé avec succès',
            'data' => [
                'message_id' => $messageId
            ]
        ], 201);
    }
}
