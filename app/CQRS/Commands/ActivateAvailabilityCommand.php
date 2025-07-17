<?php

declare(strict_types=1);

namespace App\CQRS\Commands;

final readonly class ActivateAvailabilityCommand implements CommandInterface
{
    /**
     * ActivateAvailabilityCommand constructor.
     *
     * @param int $availabilityId
     */
    public function __construct(
        private readonly int $availabilityId
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
}
