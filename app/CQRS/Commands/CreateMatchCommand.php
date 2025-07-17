<?php

declare(strict_types=1);

namespace App\CQRS\Commands;

final readonly class CreateMatchCommand implements CommandInterface
{
    /**
     * CreateMatchCommand constructor.
     *
     * @param int $userId1
     * @param int $userId2
     * @param int $availabilityId1
     * @param int $availabilityId2
     * @param int|null $restaurantId
     */
    public function __construct(
        private readonly int $userId1,
        private readonly int $userId2,
        private readonly int $availabilityId1,
        private readonly int $availabilityId2,
        private readonly ?int $restaurantId = null
    ) {
    }

    /**
     * Get the first user ID.
     *
     * @return int
     */
    public function getUserId1(): int
    {
        return $this->userId1;
    }

    /**
     * Get the second user ID.
     *
     * @return int
     */
    public function getUserId2(): int
    {
        return $this->userId2;
    }

    /**
     * Get the first availability ID.
     *
     * @return int
     */
    public function getAvailabilityId1(): int
    {
        return $this->availabilityId1;
    }

    /**
     * Get the second availability ID.
     *
     * @return int
     */
    public function getAvailabilityId2(): int
    {
        return $this->availabilityId2;
    }

    /**
     * Get the restaurant ID.
     *
     * @return int|null
     */
    public function getRestaurantId(): ?int
    {
        return $this->restaurantId;
    }
}
