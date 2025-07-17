<?php

declare(strict_types=1);

namespace App\CQRS\QueryHandlers;

use App\CQRS\Queries\GetUserRatingsQuery;
use App\CQRS\Queries\QueryInterface;
use App\Interfaces\RatingRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use InvalidArgumentException;

final readonly class GetUserRatingsQueryHandler implements QueryHandlerInterface
{
    /**
     * GetUserRatingsQueryHandler constructor.
     *
     * @param RatingRepositoryInterface $ratingRepository
     */
    public function __construct(
        private readonly RatingRepositoryInterface $ratingRepository
    ) {
    }

    /**
     * Handle the query.
     *
     * @param QueryInterface $query
     * @return Collection
     * @throws InvalidArgumentException
     */
    public function handle(QueryInterface $query): Collection
    {
        if (!$query instanceof GetUserRatingsQuery) {
            throw new InvalidArgumentException('Query must be an instance of GetUserRatingsQuery');
        }

        return $this->ratingRepository->findByRatedUserId($query->getUserId());
    }
}
