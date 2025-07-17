<?php

declare(strict_types=1);

namespace App\CQRS\QueryHandlers;

use App\CQRS\Queries\FindRestaurantsByLocationQuery;
use App\CQRS\Queries\QueryInterface;
use App\Interfaces\RestaurantRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use InvalidArgumentException;

final readonly class FindRestaurantsByLocationQueryHandler implements QueryHandlerInterface
{
    /**
     * FindRestaurantsByLocationQueryHandler constructor.
     *
     * @param RestaurantRepositoryInterface $restaurantRepository
     */
    public function __construct(
        private readonly RestaurantRepositoryInterface $restaurantRepository
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
        if (!$query instanceof FindRestaurantsByLocationQuery) {
            throw new InvalidArgumentException('Query must be an instance of FindRestaurantsByLocationQuery');
        }

        return $this->restaurantRepository->findByLocation(
            $query->getLatitude(),
            $query->getLongitude(),
            $query->getRadius(),
            $query->getCuisineTypes(),
            $query->getPriceRanges(),
            $query->getLimit(),
            $query->getOffset()
        );
    }
}
