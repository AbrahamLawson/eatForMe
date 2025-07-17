<?php

declare(strict_types=1);

namespace App\CQRS\CommandHandlers;

use App\CQRS\Commands\CommandInterface;
use App\CQRS\Commands\CreateRatingCommand;
use App\Interfaces\RatingRepositoryInterface;
use App\Interfaces\UserMatchRepositoryInterface;
use App\Models\Rating;
use App\Models\UserMatch;
use InvalidArgumentException;

final readonly class CreateRatingCommandHandler implements CommandHandlerInterface
{
    /**
     * CreateRatingCommandHandler constructor.
     *
     * @param RatingRepositoryInterface $ratingRepository
     * @param UserMatchRepositoryInterface $userMatchRepository
     */
    public function __construct(
        private readonly RatingRepositoryInterface $ratingRepository,
        private readonly UserMatchRepositoryInterface $userMatchRepository
    ) {
    }

    /**
     * Handle the command.
     *
     * @param CommandInterface $command
     * @return Rating
     * @throws InvalidArgumentException
     */
    public function handle(CommandInterface $command): Rating
    {
        if (!$command instanceof CreateRatingCommand) {
            throw new InvalidArgumentException('Command must be an instance of CreateRatingCommand');
        }

        // Verify that the match exists and is confirmed
        $match = $this->userMatchRepository->findById($command->getMatchId());
        if (!$match) {
            throw new InvalidArgumentException("Match with ID {$command->getMatchId()} not found");
        }

        if ($match->status !== UserMatch::STATUS_CONFIRMED) {
            throw new InvalidArgumentException("Cannot rate a match that is not confirmed");
        }

        // Verify that the user is part of the match
        $userId = $command->getUserId();
        if ($match->user_id_1 !== $userId && $match->user_id_2 !== $userId) {
            throw new InvalidArgumentException("User with ID {$userId} is not part of match with ID {$match->id}");
        }

        // Verify that the rated user is part of the match
        $ratedUserId = $command->getRatedUserId();
        if ($match->user_id_1 !== $ratedUserId && $match->user_id_2 !== $ratedUserId) {
            throw new InvalidArgumentException("User with ID {$ratedUserId} is not part of match with ID {$match->id}");
        }

        // Verify that the user is not rating themselves
        if ($userId === $ratedUserId) {
            throw new InvalidArgumentException("User cannot rate themselves");
        }

        // Verify that the score is between 1 and 5
        $score = $command->getScore();
        if ($score < 1 || $score > 5) {
            throw new InvalidArgumentException("Score must be between 1 and 5");
        }

        // Create rating data
        $ratingData = [
            'user_id' => $userId,
            'rated_user_id' => $ratedUserId,
            'match_id' => $command->getMatchId(),
            'score' => $score,
            'comment' => $command->getComment()
        ];

        return $this->ratingRepository->create($ratingData);
    }
}
