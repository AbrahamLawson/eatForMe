<?php

declare(strict_types=1);

namespace App\Http\Controllers\Profile;

use App\CQRS\CommandHandlers\CreateProfileCommandHandler;
use App\CQRS\Commands\CreateProfileCommand;
use App\DTO\ProfileDTO;
use App\Http\Requests\CreateProfileRequest;
use Illuminate\Http\JsonResponse;

final readonly class CreateProfileController
{
    /**
     * Handle the incoming request.
     *
     * @param CreateProfileRequest $request
     * @param CreateProfileCommandHandler $handler
     * @return JsonResponse
     */
    public function __invoke(CreateProfileRequest $request, CreateProfileCommandHandler $handler): JsonResponse
    {
        // Récupérer les données validées
        $validated = $request->validated();
        
        // Créer le DTO
        $profileDTO = new ProfileDTO(
            $validated['bio'] ?? null,
            $validated['preferences'] ?? [],
            $validated['dietary_restrictions'] ?? [],
            $validated['favorite_cuisines'] ?? [],
            $validated['avatar_url'] ?? null
        );
        
        // Créer la commande
        $command = new CreateProfileCommand(
            $request->user()->id,
            $profileDTO
        );
        
        // Exécuter la commande
        $profile = $handler->handle($command);
        
        // Retourner la réponse
        return response()->json([
            'message' => 'Profile created successfully',
            'data' => $profile
        ], 201);
    }
}
