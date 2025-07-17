<?php

declare(strict_types=1);

namespace App\Services;

use App\Interfaces\RatingRepositoryInterface;
use App\Interfaces\UserMatchRepositoryInterface;
use App\Models\Rating;
use Illuminate\Database\Eloquent\Collection;

class RatingService
{
    /**
     * RatingService constructor.
     *
     * @param RatingRepositoryInterface $ratingRepository
     * @param UserMatchRepositoryInterface $userMatchRepository
     */
    public function __construct(
        private RatingRepositoryInterface $ratingRepository,
        private UserMatchRepositoryInterface $userMatchRepository
    ) {}

    /**
     * Create a rating for a user after a match.
     *
     * @param int $userId
     * @param int $ratedUserId
     * @param int $userMatchId
     * @param int $score
     * @param string|null $comment
     * @return Rating
     */
    public function rateUser(int $userId, int $ratedUserId, int $userMatchId, int $score, ?string $comment = null): Rating
    {
        // Vérifier que le match existe et est valide
        $match = $this->userMatchRepository->findById($userMatchId);
        
        if (!$match) {
            throw new \InvalidArgumentException("Match not found");
        }
        
        // Vérifier que les utilisateurs font partie du match
        if (($match->user_id !== $userId && $match->matched_user_id !== $userId) || 
            ($match->user_id !== $ratedUserId && $match->matched_user_id !== $ratedUserId)) {
            throw new \InvalidArgumentException("Users are not part of this match");
        }
        
        // Vérifier que le match est terminé
        if ($match->status !== 'completed') {
            throw new \InvalidArgumentException("Cannot rate a match that is not completed");
        }
        
        // Vérifier que le score est valide (entre 1 et 5)
        if ($score < 1 || $score > 5) {
            throw new \InvalidArgumentException("Score must be between 1 and 5");
        }
        
        // Vérifier que l'utilisateur n'a pas déjà noté cet utilisateur pour ce match
        $existingRating = $this->ratingRepository->findByUserMatchId($userMatchId)
            ->where('user_id', $userId)
            ->where('rated_user_id', $ratedUserId)
            ->first();
            
        if ($existingRating) {
            throw new \InvalidArgumentException("User has already rated this match");
        }
        
        // Créer la notation
        $data = [
            'user_id' => $userId,
            'rated_user_id' => $ratedUserId,
            'user_match_id' => $userMatchId,
            'score' => $score,
            'comment' => $comment,
            'rated_at' => now(),
        ];
        
        return $this->ratingRepository->create($data);
    }

    /**
     * Get the average rating for a user.
     *
     * @param int $userId
     * @return float
     */
    public function getAverageRating(int $userId): float
    {
        return $this->ratingRepository->getAverageRatingForUser($userId);
    }

    /**
     * Get all ratings for a user.
     *
     * @param int $userId
     * @return Collection
     */
    public function getUserRatings(int $userId): Collection
    {
        return $this->ratingRepository->findByRatedUserId($userId);
    }
}
