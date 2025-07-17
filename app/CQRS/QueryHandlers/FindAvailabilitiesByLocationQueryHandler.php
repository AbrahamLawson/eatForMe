<?php

declare(strict_types=1);

namespace App\CQRS\QueryHandlers;

use App\CQRS\Queries\FindAvailabilitiesByLocationQuery;
use App\CQRS\Queries\QueryInterface;
use App\Interfaces\AvailabilityRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use InvalidArgumentException;

final readonly class FindAvailabilitiesByLocationQueryHandler implements QueryHandlerInterface
{
    /**
     * FindAvailabilitiesByLocationQueryHandler constructor.
     *
     * @param AvailabilityRepositoryInterface $availabilityRepository
     */
    public function __construct(
        private readonly AvailabilityRepositoryInterface $availabilityRepository
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
        if (!$query instanceof FindAvailabilitiesByLocationQuery) {
            throw new InvalidArgumentException('Query must be an instance of FindAvailabilitiesByLocationQuery');
        }

        return $this->availabilityRepository->findByLocation(
            $query->getLatitude(),
            $query->getLongitude(),
            $query->getRadius(),
            $query->getStartTime(),
            $query->getEndTime(),
            $query->getPreferences(),
            $query->getActiveOnly()
        );
    }
}
