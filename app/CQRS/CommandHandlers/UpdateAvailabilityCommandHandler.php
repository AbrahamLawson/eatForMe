<?php

declare(strict_types=1);

namespace App\CQRS\CommandHandlers;

use App\CQRS\Commands\CommandInterface;
use App\CQRS\Commands\UpdateAvailabilityCommand;
use App\Interfaces\AvailabilityRepositoryInterface;
use App\Models\Availability;
use InvalidArgumentException;

final readonly class UpdateAvailabilityCommandHandler implements CommandHandlerInterface
{
    /**
     * UpdateAvailabilityCommandHandler constructor.
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
        if (!$command instanceof UpdateAvailabilityCommand) {
            throw new InvalidArgumentException('Command must be an instance of UpdateAvailabilityCommand');
        }

        $availabilityId = $command->getAvailabilityId();
        $dto = $command->getAvailabilityDTO();

        // Validation simplifiée - plus besoin de vérifier les plages horaires

        // Check if availability exists
        $availability = $this->availabilityRepository->findById($availabilityId);
        if (!$availability) {
            throw new InvalidArgumentException("Availability with ID {$availabilityId} not found");
        }

        // Update availability
        $data = [
            'user_id' => $dto->userId,
            'latitude' => $dto->getLocationLat(),
            'longitude' => $dto->getLocationLng(),
            'preferences' => $dto->getPreferences() ? json_encode($dto->getPreferences()) : null,
            'is_active' => $dto->isActive()
        ];

        return $this->availabilityRepository->update($availabilityId, $data);
    }
}
