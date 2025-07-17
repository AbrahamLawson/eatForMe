<?php

declare(strict_types=1);

namespace App\CQRS\CommandHandlers;

use App\CQRS\Commands\CommandInterface;
use App\CQRS\Commands\SendMessageCommand;
use App\Interfaces\MessageRepositoryInterface;
use App\Interfaces\UserMatchRepositoryInterface;
use App\Models\Message;
use InvalidArgumentException;

final readonly class SendMessageCommandHandler implements CommandHandlerInterface
{
    /**
     * SendMessageCommandHandler constructor.
     *
     * @param MessageRepositoryInterface $messageRepository
     * @param UserMatchRepositoryInterface $userMatchRepository
     */
    public function __construct(
        private readonly MessageRepositoryInterface $messageRepository,
        private readonly UserMatchRepositoryInterface $userMatchRepository
    ) {
    }

    /**
     * Handle the command.
     *
     * @param CommandInterface $command
     * @return Message
     * @throws InvalidArgumentException
     */
    public function handle(CommandInterface $command): Message
    {
        if (!$command instanceof SendMessageCommand) {
            throw new InvalidArgumentException('Command must be an instance of SendMessageCommand');
        }

        // Verify that the match exists and both users are part of it
        $match = $this->userMatchRepository->findById($command->getMatchId());
        if (!$match) {
            throw new InvalidArgumentException("Match with ID {$command->getMatchId()} not found");
        }

        // Verify that the sender is part of the match
        $senderId = $command->getSenderId();
        if ($match->user_id_1 !== $senderId && $match->user_id_2 !== $senderId) {
            throw new InvalidArgumentException("User with ID {$senderId} is not part of match with ID {$match->id}");
        }

        // Verify that the receiver is part of the match
        $receiverId = $command->getReceiverId();
        if ($match->user_id_1 !== $receiverId && $match->user_id_2 !== $receiverId) {
            throw new InvalidArgumentException("User with ID {$receiverId} is not part of match with ID {$match->id}");
        }

        // Create message data
        $messageData = [
            'match_id' => $command->getMatchId(),
            'sender_id' => $command->getSenderId(),
            'receiver_id' => $command->getReceiverId(),
            'content' => $command->getContent(),
            'read' => false
        ];

        return $this->messageRepository->create($messageData);
    }
}
