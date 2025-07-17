<?php

declare(strict_types=1);

namespace App\Services;

use App\Interfaces\MessageRepositoryInterface;
use App\Interfaces\UserMatchRepositoryInterface;
use App\Models\Message;
use Illuminate\Database\Eloquent\Collection;

class MessageService
{
    /**
     * MessageService constructor.
     *
     * @param MessageRepositoryInterface $messageRepository
     * @param UserMatchRepositoryInterface $userMatchRepository
     */
    public function __construct(
        private MessageRepositoryInterface $messageRepository,
        private UserMatchRepositoryInterface $userMatchRepository
    ) {}

    /**
     * Send a message from one user to another.
     *
     * @param int $senderId
     * @param int $receiverId
     * @param int $userMatchId
     * @param string $content
     * @return Message
     */
    public function sendMessage(int $senderId, int $receiverId, int $userMatchId, string $content): Message
    {
        // Vérifier que le match existe et est valide
        $match = $this->userMatchRepository->findById($userMatchId);
        
        if (!$match) {
            throw new \InvalidArgumentException("Match not found");
        }
        
        // Vérifier que les utilisateurs font partie du match
        if (($match->user_id !== $senderId && $match->matched_user_id !== $senderId) || 
            ($match->user_id !== $receiverId && $match->matched_user_id !== $receiverId)) {
            throw new \InvalidArgumentException("Users are not part of this match");
        }
        
        // Vérifier que le match est accepté
        if ($match->status !== 'accepted') {
            throw new \InvalidArgumentException("Cannot send message for a match that is not accepted");
        }
        
        // Créer le message
        $data = [
            'sender_id' => $senderId,
            'receiver_id' => $receiverId,
            'user_match_id' => $userMatchId,
            'content' => $content,
            'is_read' => false,
            'sent_at' => now(),
        ];
        
        return $this->messageRepository->create($data);
    }

    /**
     * Mark a message as read.
     *
     * @param int $messageId
     * @return Message
     */
    public function markAsRead(int $messageId): Message
    {
        return $this->messageRepository->update($messageId, ['is_read' => true, 'read_at' => now()]);
    }

    /**
     * Get conversation between two users.
     *
     * @param int $userId1
     * @param int $userId2
     * @return Collection
     */
    public function getConversation(int $userId1, int $userId2): Collection
    {
        return $this->messageRepository->findConversationBetweenUsers($userId1, $userId2);
    }

    /**
     * Get unread messages for a user.
     *
     * @param int $userId
     * @return Collection
     */
    public function getUnreadMessages(int $userId): Collection
    {
        return $this->messageRepository->findUnreadByUserId($userId);
    }
}
