<?php

declare(strict_types=1);

namespace App\CQRS\Queries;

final readonly class FindAvailabilitiesByUserQuery implements QueryInterface
{
    /**
     * FindAvailabilitiesByUserQuery constructor.
     *
     * @param int $userId
     * @param bool|null $activeOnly
     */
    public function __construct(
        private readonly int $userId,
        private readonly ?bool $activeOnly = null
    ) {
    }

    /**
     * Get the user ID.
     *
     * @return int
     */
    public function getUserId(): int
    {
        return $this->userId;
    }

    /**
     * Get active only flag.
     *
     * @return bool|null
     */
    public function getActiveOnly(): ?bool
    {
        return $this->activeOnly;
    }
}
