<?php

declare(strict_types=1);

namespace App\CQRS\QueryHandlers;

use App\CQRS\Queries\FindPotentialMatchesQuery;
use App\CQRS\Queries\QueryInterface;
use App\Interfaces\AvailabilityRepositoryInterface;
use App\Models\Availability;
use Illuminate\Database\Eloquent\Collection;
use InvalidArgumentException;

final readonly class FindPotentialMatchesQueryHandler implements QueryHandlerInterface
{
    /**
     * FindPotentialMatchesQueryHandler constructor.
     *
     * @param AvailabilityRepositoryInterface $availabilityRepository
     */
    public function __construct(
        private AvailabilityRepositoryInterface $availabilityRepository
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
        if (!$query instanceof FindPotentialMatchesQuery) {
            throw new InvalidArgumentException('Query must be an instance of FindPotentialMatchesQuery');
        }

        // Find potential matches based on location, time, and preferences
        return $this->availabilityRepository->findPotentialMatches(
            $query->getUserId(),
            $query->getLatitude(),
            $query->getLongitude(),
            $query->getRadius(),
            $query->getStartTime(),
            $query->getEndTime(),
            $query->getPreferences()
        );
    }
}
