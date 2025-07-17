<?php

declare(strict_types=1);

namespace App\CQRS\CommandHandlers;

use App\CQRS\Commands\ActivateAvailabilityCommand;
use App\CQRS\Commands\CommandInterface;
use App\Interfaces\AvailabilityRepositoryInterface;
use App\Models\Availability;
use InvalidArgumentException;

final readonly class ActivateAvailabilityCommandHandler implements CommandHandlerInterface
{
    /**
     * ActivateAvailabilityCommandHandler constructor.
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
        if (!$command instanceof ActivateAvailabilityCommand) {
            throw new InvalidArgumentException('Command must be an instance of ActivateAvailabilityCommand');
        }

        $availabilityId = $command->getAvailabilityId();

        // Check if availability exists
        $availability = $this->availabilityRepository->findById($availabilityId);
        if (!$availability) {
            throw new InvalidArgumentException("Availability with ID {$availabilityId} not found");
        }

        // Update availability status
        return $this->availabilityRepository->update($availabilityId, ['is_active' => true]);
    }
}
