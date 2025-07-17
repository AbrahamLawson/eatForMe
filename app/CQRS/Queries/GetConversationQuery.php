<?php

declare(strict_types=1);

namespace App\CQRS\Queries;

final readonly class GetConversationQuery implements QueryInterface
{
    /**
     * GetConversationQuery constructor.
     *
     * @param int $matchId
     * @param int|null $limit
     * @param int|null $offset
     */
    public function __construct(
        private int $matchId,
        private ?int $limit = 20,
        private ?int $offset = 0
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
     * Get the limit.
     *
     * @return int|null
     */
    public function getLimit(): ?int
    {
        return $this->limit;
    }

    /**
     * Get the offset.
     *
     * @return int|null
     */
    public function getOffset(): ?int
    {
        return $this->offset;
    }
}
