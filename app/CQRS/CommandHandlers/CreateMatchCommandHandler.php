<?php

declare(strict_types=1);

namespace App\CQRS\CommandHandlers;

use App\CQRS\Commands\CommandInterface;
use App\CQRS\Commands\CreateMatchCommand;
use App\Interfaces\UserMatchRepositoryInterface;
use App\Models\UserMatch;
use InvalidArgumentException;

final readonly class CreateMatchCommandHandler implements CommandHandlerInterface
{
    /**
     * CreateMatchCommandHandler constructor.
     *
     * @param UserMatchRepositoryInterface $userMatchRepository
     */
    public function __construct(
        private UserMatchRepositoryInterface $userMatchRepository
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
        
        // Récupérer et retourner l'objet match créé
        return $this->userMatchRepository->findById($matchId);
    }
}
