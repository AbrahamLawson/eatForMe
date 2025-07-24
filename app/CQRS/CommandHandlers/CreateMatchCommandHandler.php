<?php

declare(strict_types=1);

namespace App\CQRS\CommandHandlers;

use App\CQRS\Commands\CommandInterface;
use App\CQRS\Commands\CreateMatchCommand;
use App\Interfaces\UserMatchRepositoryInterface;
use App\Models\UserMatch;
use App\Services\MatchCompatibilityService;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

final readonly class CreateMatchCommandHandler implements CommandHandlerInterface
{
    /**
     * CreateMatchCommandHandler constructor.
     *
     * @param UserMatchRepositoryInterface $userMatchRepository
     * @param MatchCompatibilityService $compatibilityService
     */
    public function __construct(
        private UserMatchRepositoryInterface $userMatchRepository,
        private MatchCompatibilityService $compatibilityService
    ) {
    }

    /**
     * Handle the command.
     *
     * @param CommandInterface $command
     * @return UserMatch
     * @throws InvalidArgumentException
     */
    public function handle(CommandInterface $command): UserMatch
    {
        if (!$command instanceof CreateMatchCommand) {
            throw new InvalidArgumentException('Command must be an instance of CreateMatchCommand');
        }

        // Récupérer les IDs des disponibilités
        $initiatorAvailabilityId = $command->getAvailabilityId1();
        $receiverAvailabilityId = $command->getAvailabilityId2();
        
        // Créer le match en utilisant la nouvelle méthode
        $matchId = $this->userMatchRepository->createMatch($initiatorAvailabilityId, $receiverAvailabilityId);
        
        // Récupérer l'objet match créé
        $match = $this->userMatchRepository->findById($matchId);
        
        // Calculer et enregistrer le score de compatibilité
        try {
            $score = $this->compatibilityService->calculateAndSaveMatchScore($matchId);
            if ($score !== null) {
                Log::info('Score de compatibilité calculé avec succès pour le match', [
                    'match_id' => $matchId,
                    'compatibility_score' => $score
                ]);
            }
        } catch (\Exception $e) {
            // Ne pas bloquer la création du match si le calcul du score échoue
            Log::error('Erreur lors du calcul du score de compatibilité', [
                'match_id' => $matchId,
                'error' => $e->getMessage()
            ]);
        }
        
        return $match;
    }
}
