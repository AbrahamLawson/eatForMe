<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\DTO\MessageDTO;
use App\Models\Message;
use App\Repositories\Interfaces\MessageRepositoryInterface;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model;

final class EloquentMessageRepository extends AbstractEloquentRepository implements MessageRepositoryInterface
{
    /**
     * @param Message $message
     */
    public function __construct(
        private Message $message
    ) {
        $this->model = $message;
    }

    /**
     * Envoie un nouveau message
     *
     * @param MessageDTO $messageDTO
     * @return MessageDTO
     */
    public function send(MessageDTO $messageDTO): MessageDTO
    {
        $message = $this->message->create([
            'match_id' => $messageDTO->getMatchId(),
            'sender_id' => $messageDTO->getSenderId(),
            'content' => $messageDTO->getContent(),
            'read_at' => null,
        ]);

        return MessageDTO::fromArray($message->toArray());
    }

    /**
     * Marque un message comme lu
     *
     * @param int $id
     * @return bool
     */
    public function markAsRead(int $id): bool
    {
        $message = $this->message->find($id);
        
        if (!$message) {
            return false;
        }
        
        $message->read_at = now();
        return $message->save();
    }

    /**
     * Récupère une conversation entre deux utilisateurs
     *
     * @param int $matchId
     * @return EloquentCollection
     */
    public function getConversation(int $matchId): EloquentCollection
    {
        $messages = $this->message
            ->where('match_id', $matchId)
            ->orderBy('created_at', 'asc')
            ->get();
        
        return $messages->map(function ($message) {
            return MessageDTO::fromArray($message->toArray());
        });
    }
}
