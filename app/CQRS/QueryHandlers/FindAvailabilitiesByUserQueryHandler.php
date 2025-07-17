<?php

declare(strict_types=1);

namespace App\CQRS\QueryHandlers;

use App\CQRS\Queries\FindAvailabilitiesByUserQuery;
use App\CQRS\Queries\QueryInterface;
use App\Interfaces\AvailabilityRepositoryInterface;
use App\Models\Availability;
use Illuminate\Database\Eloquent\Collection;
use InvalidArgumentException;

final readonly class FindAvailabilitiesByUserQueryHandler implements QueryHandlerInterface
{
    /**
     * FindAvailabilitiesByUserQueryHandler constructor.
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
        if (!$query instanceof FindAvailabilitiesByUserQuery) {
            throw new InvalidArgumentException('Query must be an instance of FindAvailabilitiesByUserQuery');
        }

        $userId = $query->getUserId();
        $activeOnly = $query->getActiveOnly();

        return $this->availabilityRepository->findByUser($userId, $activeOnly);
    }
}
