<?php

declare(strict_types=1);

namespace App\CQRS\CommandHandlers;

use App\CQRS\Commands\CommandInterface;
use App\CQRS\Commands\CreateAvailabilityCommand;
use App\Interfaces\AvailabilityRepositoryInterface;
use App\Models\Availability;
use InvalidArgumentException;

final readonly class CreateAvailabilityCommandHandler implements CommandHandlerInterface
{
    /**
     * CreateAvailabilityCommandHandler constructor.
     *
     * @param AvailabilityRepositoryInterface $availabilityRepository
     */
    public function __construct(
        private readonly AvailabilityRepositoryInterface $availabilityRepository
    ) {
    }

    /**
     * Handle the command.
     *
     * @param CommandInterface $command
     * @return mixed
     * @throws InvalidArgumentException
     */
    public function handle(CommandInterface $command): Availability
    {
        if (!$command instanceof CreateAvailabilityCommand) {
            throw new InvalidArgumentException('Command must be an instance of CreateAvailabilityCommand');
        }

        $dto = $command->getAvailabilityDTO();

        // Validation simplifiée - plus besoin de vérifier les plages horaires

        // Passer directement le DTO au repository
        return $this->availabilityRepository->createFromDTO($dto);
    }
}
