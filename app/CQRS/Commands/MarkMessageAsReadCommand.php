<?php

declare(strict_types=1);

namespace App\CQRS\Commands;

final readonly class MarkMessageAsReadCommand implements CommandInterface
{
    /**
     * MarkMessageAsReadCommand constructor.
     *
     * @param int $messageId
     * @param int $userId
     */
    public function __construct(
        private int $messageId,
        private int $userId
    ) {
    }

    /**
     * Get the message ID.
     *
     * @return int
     */
    public function getMessageId(): int
    {
        return $this->messageId;
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
