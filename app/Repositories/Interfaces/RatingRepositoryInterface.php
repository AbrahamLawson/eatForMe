<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use App\Models\Rating;
use Illuminate\Database\Eloquent\Collection;

interface RatingRepositoryInterface extends RepositoryInterface
{
    /**
     * Find ratings given by a specific user.
     *
     * @param int $userId
     * @return Collection
     */
    public function findByUserId(int $userId): Collection;
    
    /**
     * Find ratings received by a specific user.
     *
     * @param int $ratedUserId
     * @return Collection
     */
    public function findByRatedUserId(int $ratedUserId): Collection;
    
    /**
     * Find ratings for a specific user match.
     *
     * @param int $userMatchId
     * @return Collection
     */
    public function findByUserMatchId(int $userMatchId): Collection;
    
    /**
     * Calculate average rating for a user.
     *
     * @param int $userId
     * @return float
     */
    public function getAverageRatingForUser(int $userId): float;
}
