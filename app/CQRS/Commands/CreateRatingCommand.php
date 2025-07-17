<?php

declare(strict_types=1);

namespace App\CQRS\Commands;

final readonly class CreateRatingCommand implements CommandInterface
{
    /**
     * CreateRatingCommand constructor.
     *
     * @param int $userId
     * @param int $ratedUserId
     * @param int $matchId
     * @param int $score
     * @param string|null $comment
     */
    public function __construct(
        private readonly int $userId,
        private readonly int $ratedUserId,
        private readonly int $matchId,
        private readonly int $score,
        private readonly ?string $comment = null
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
     * Get the rated user ID.
     *
     * @return int
     */
    public function getRatedUserId(): int
    {
        return $this->ratedUserId;
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
     * Get the score.
     *
     * @return int
     */
    public function getScore(): int
    {
        return $this->score;
    }

    /**
     * Get the comment.
     *
     * @return string|null
     */
    public function getComment(): ?string
    {
        return $this->comment;
    }
}
