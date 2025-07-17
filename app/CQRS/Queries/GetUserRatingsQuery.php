<?php

declare(strict_types=1);

namespace App\CQRS\Queries;

final readonly class GetUserRatingsQuery implements QueryInterface
{
    /**
     * GetUserRatingsQuery constructor.
     *
     * @param int $userId
     */
    public function __construct(
        private readonly int $userId
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
}
