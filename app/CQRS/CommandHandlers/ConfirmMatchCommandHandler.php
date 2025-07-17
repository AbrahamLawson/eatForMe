<?php

declare(strict_types=1);

namespace App\CQRS\CommandHandlers;

use App\CQRS\Commands\CommandInterface;
use App\CQRS\Commands\ConfirmMatchCommand;
use App\Interfaces\UserMatchRepositoryInterface;
use App\Models\UserMatch;
use InvalidArgumentException;

final readonly class ConfirmMatchCommandHandler implements CommandHandlerInterface
{
    /**
     * ConfirmMatchCommandHandler constructor.
     *
     * @param UserMatchRepositoryInterface $userMatchRepository
     */
    public function __construct(
        private readonly UserMatchRepositoryInterface $userMatchRepository
    ) {
    }

    /**
     * Handle the command.
     *
     * @param CommandInterface $command
     * @return UserMatch
     * @throws InvalidArgumentException
     */
    public function handle(CommandInterface $command): UserMatch
    {
        if (!$command instanceof ConfirmMatchCommand) {
            throw new InvalidArgumentException('Command must be an instance of ConfirmMatchCommand');
        }

        $matchId = $command->getMatchId();
        $userId = $command->getUserId();

        // Get the match
        $match = $this->userMatchRepository->findById($matchId);
        if (!$match) {
            throw new InvalidArgumentException("Match with ID {$matchId} not found");
        }

        // Check if user is part of the match
        if ($match->user_id_1 !== $userId && $match->user_id_2 !== $userId) {
            throw new InvalidArgumentException("User with ID {$userId} is not part of match with ID {$matchId}");
        }

        // Update confirmation status
        $updateData = [];
        if ($match->user_id_1 === $userId) {
            $updateData['user_1_confirmed'] = true;
        } else {
            $updateData['user_2_confirmed'] = true;
        }

        // Update match status if both users confirmed
        $updatedMatch = $this->userMatchRepository->update($matchId, $updateData);
        if ($updatedMatch->user_1_confirmed && $updatedMatch->user_2_confirmed) {
            $this->userMatchRepository->update($matchId, ['status' => UserMatch::STATUS_CONFIRMED]);
        }

        return $this->userMatchRepository->findById($matchId);
    }
}
