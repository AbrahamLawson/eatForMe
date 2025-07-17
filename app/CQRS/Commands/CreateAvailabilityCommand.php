<?php

declare(strict_types=1);

namespace App\CQRS\Commands;

use App\DTO\AvailabilityDTO;

final readonly class CreateAvailabilityCommand implements CommandInterface
{
    /**
     * CreateAvailabilityCommand constructor.
     *
     * @param AvailabilityDTO $availabilityDTO
     */
    public function __construct(
        private readonly AvailabilityDTO $availabilityDTO
    ) {}

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
