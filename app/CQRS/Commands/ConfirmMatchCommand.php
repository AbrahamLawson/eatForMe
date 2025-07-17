<?php

declare(strict_types=1);

namespace App\CQRS\Commands;

final readonly class ConfirmMatchCommand implements CommandInterface
{
    /**
     * ConfirmMatchCommand constructor.
     *
     * @param int $matchId
     * @param int $userId
     */
    public function __construct(
        private readonly int $matchId,
        private readonly int $userId
    ) {
    }

    /**
     * Get the match ID.
     *
     * @return int
     */
    public function getMatchId(): int
    {
        return $this->matchId;
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
