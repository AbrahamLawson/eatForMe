<?php

declare(strict_types=1);

namespace App\DTO;

final readonly class RatingDTO
{
    /**
     * RatingDTO constructor.
     *
     * @param int $matchId
     * @param int $raterId
     * @param int $ratedId
     * @param int $score
     * @param string|null $comment
     */
    public function __construct(
        private int $matchId,
        private int $raterId,
        private int $ratedId,
        private int $score,
        private ?string $comment = null
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
     * Get the rater ID.
     *
     * @return int
     */
    public function getRaterId(): int
    {
        return $this->raterId;
    }

    /**
     * Get the rated ID.
     *
     * @return int
     */
    public function getRatedId(): int
    {
        return $this->ratedId;
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

    /**
     * Create from array.
     *
     * @param array $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            $data['match_id'],
            $data['rater_id'],
            $data['rated_id'],
            $data['score'],
            $data['comment'] ?? null
        );
    }

    /**
     * Convert to array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'match_id' => $this->matchId,
            'rater_id' => $this->raterId,
            'rated_id' => $this->ratedId,
            'score' => $this->score,
            'comment' => $this->comment,
        ];
    }
}
