<?php

declare(strict_types=1);

namespace App\Http\Controllers\Profile;

use App\CQRS\CommandHandlers\UpdateProfileCommandHandler;
use App\CQRS\Commands\UpdateProfileCommand;
use App\DTO\ProfileDTO;
use App\Http\Requests\UpdateProfileRequest;
use Illuminate\Http\JsonResponse;

final readonly class UpdateProfileController
{
    /**
     * Handle the incoming request.
     *
     * @param UpdateProfileRequest $request
     * @param UpdateProfileCommandHandler $handler
     * @return JsonResponse
     */
    public function __invoke(UpdateProfileRequest $request, UpdateProfileCommandHandler $handler): JsonResponse
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
        $command = new UpdateProfileCommand(
            $request->user()->id,
            $profileDTO
        );
        
        // Exécuter la commande
        $profile = $handler->handle($command);
        
        // Retourner la réponse
        return response()->json([
            'message' => 'Profile updated successfully',
            'data' => $profile
        ]);
    }
}
