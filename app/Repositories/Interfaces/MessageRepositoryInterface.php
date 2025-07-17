<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use App\Models\Message;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;

interface MessageRepositoryInterface extends RepositoryInterface
{
    /**
     * Find messages by sender ID.
     *
     * @param int $senderId
     * @return EloquentCollection
     */
    public function findBySenderId(int $senderId): EloquentCollection;
    
    /**
     * Find messages by receiver ID.
     *
     * @param int $receiverId
     * @return EloquentCollection
     */
    public function findByReceiverId(int $receiverId): EloquentCollection;
    
    /**
     * Find messages by user match ID.
     *
     * @param int $userMatchId
     * @return EloquentCollection
     */
    public function findByUserMatchId(int $userMatchId): EloquentCollection;
    
    /**
     * Find unread messages for a specific user.
     *
     * @param int $userId
     * @return EloquentCollection
     */
    public function findUnreadByUserId(int $userId): EloquentCollection;
    
    /**
     * Find conversation between two users.
     *
     * @param int $userOneId
     * @param int $userTwoId
     * @return EloquentCollection
     */
    public function findConversationBetweenUsers(int $userOneId, int $userTwoId): EloquentCollection;
}
