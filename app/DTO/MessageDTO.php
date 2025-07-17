<?php

declare(strict_types=1);

namespace App\DTO;

final readonly class MessageDTO
{
    /**
     * MessageDTO constructor.
     *
     * @param int $matchId
     * @param int $senderId
     * @param int $receiverId
     * @param string $content
     * @param bool $read
     */
    public function __construct(
        private int $matchId,
        private int $senderId,
        private int $receiverId,
        private string $content,
        private bool $read = false
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
     * Get the content.
     *
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * Check if the message is read.
     *
     * @return bool
     */
    public function isRead(): bool
    {
        return $this->read;
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
            $data['sender_id'],
            $data['receiver_id'],
            $data['content'],
            $data['read'] ?? false
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
            'sender_id' => $this->senderId,
            'receiver_id' => $this->receiverId,
            'content' => $this->content,
            'read' => $this->read,
        ];
    }
}
