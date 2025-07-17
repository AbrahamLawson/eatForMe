<?php

declare(strict_types=1);

namespace App\CQRS\CommandHandlers;

use App\CQRS\Commands\CommandInterface;
use App\CQRS\Commands\MarkMessageAsReadCommand;
use App\Interfaces\MessageRepositoryInterface;
use App\Models\Message;
use InvalidArgumentException;

final readonly class MarkMessageAsReadCommandHandler implements CommandHandlerInterface
{
    /**
     * MarkMessageAsReadCommandHandler constructor.
     *
     * @param MessageRepositoryInterface $messageRepository
     */
    public function __construct(
        private MessageRepositoryInterface $messageRepository
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
        if (!$command instanceof MarkMessageAsReadCommand) {
            throw new InvalidArgumentException('Command must be an instance of MarkMessageAsReadCommand');
        }

        // Get the message
        $message = $this->messageRepository->findById($command->getMessageId());
        if (!$message) {
            throw new InvalidArgumentException("Message with ID {$command->getMessageId()} not found");
        }

        // Verify that the user is the receiver of the message
        if ($message->receiver_id !== $command->getUserId()) {
            throw new InvalidArgumentException("User with ID {$command->getUserId()} is not the receiver of message with ID {$message->id}");
        }

        // Mark the message as read
        return $this->messageRepository->update($message->id, ['read' => true]);
    }
}
