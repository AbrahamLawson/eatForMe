<?php

declare(strict_types=1);

namespace App\CQRS\Commands;

use App\DTO\AvailabilityDTO;

final readonly class UpdateAvailabilityCommand implements CommandInterface
{
    /**
     * UpdateAvailabilityCommand constructor.
     *
     * @param int $availabilityId
     * @param AvailabilityDTO $availabilityDTO
     */
    public function __construct(
        private readonly int $availabilityId,
        private readonly AvailabilityDTO $availabilityDTO
    ) {
    }

    /**
     * Get the availability ID.
     *
     * @return int
     */
    public function getAvailabilityId(): int
    {
        return $this->availabilityId;
    }

    /**
     * Get the availability DTO.
     *
     * @return AvailabilityDTO
     */
    public function getAvailabilityDTO(): AvailabilityDTO
    {
        return $this->availabilityDTO;
    }
}
