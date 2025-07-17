<?php

declare(strict_types=1);

namespace App\CQRS\CommandHandlers;

use App\CQRS\Commands\ActiveSearchCommand;
use App\CQRS\Commands\CommandInterface;
use App\Models\User;
use App\Services\GeolocationService;
use InvalidArgumentException;

final readonly class ActiveSearchCommandHandler implements CommandHandlerInterface
{
    /**
     * ActiveSearchCommandHandler constructor.
     *
     * @param GeolocationService $geolocationService
     */
    public function __construct(
        private GeolocationService $geolocationService
    ) {
    }

    /**
     * Handle the command.
     *
     * @param CommandInterface $command
     * @return array
     * @throws InvalidArgumentException
     */
    public function handle(CommandInterface $command): array
    {
        if (!$command instanceof ActiveSearchCommand) {
            throw new InvalidArgumentException('Command must be an instance of ActiveSearchCommand');
        }

        // Récupérer l'utilisateur actuel
        $currentUser = User::findOrFail($command->getUserId());
        
        // Trouver les utilisateurs à proximité
        $nearbyUsers = $this->geolocationService->findNearbyUsers(
            $currentUser,
            $command->getLatitude(),
            $command->getLongitude(),
            $command->getDistance(),
            $command->getActivity(),
            $command->getProfile()
        );
        
        return $nearbyUsers;
    }
}
