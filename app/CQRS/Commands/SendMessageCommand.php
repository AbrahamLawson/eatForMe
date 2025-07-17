<?php

declare(strict_types=1);

namespace App\CQRS\Commands;

final readonly class SendMessageCommand implements CommandInterface
{
    /**
     * SendMessageCommand constructor.
     *
     * @param int $senderId
     * @param int $receiverId
     * @param int $matchId
     * @param string $content
     */
    public function __construct(
        private readonly int $senderId,
        private readonly int $receiverId,
        private readonly int $matchId,
        private readonly string $content
    ) {
    }

    /**
     * Get the sender ID.
     *
     * @return int
     */
    public function getSenderId(): int
    {
        return $this->senderId;
    }

    /**
     * Get the receiver ID.
     *
     * @return int
     */
    public function getReceiverId(): int
    {
        return $this->receiverId;
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
     * Get the message content.
     *
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }
}
