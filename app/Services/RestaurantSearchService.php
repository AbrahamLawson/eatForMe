<?php

declare(strict_types=1);

namespace App\Services;

use App\Interfaces\RestaurantRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class RestaurantSearchService
{
    /**
     * RestaurantSearchService constructor.
     *
     * @param RestaurantRepositoryInterface $restaurantRepository
     */
    public function __construct(
        private RestaurantRepositoryInterface $restaurantRepository
    ) {}

    /**
     * Find restaurants by name (partial match).
     *
     * @param string $name
     * @return Collection
     */
    public function findByName(string $name): Collection
    {
        return $this->restaurantRepository->findByName($name);
    }

    /**
     * Find restaurants near a specific location.
     *
     * @param float $latitude
     * @param float $longitude
     * @param float $radiusInKm
     * @return Collection
     */
    public function findNearLocation(float $latitude, float $longitude, float $radiusInKm = 5.0): Collection
    {
        return $this->restaurantRepository->findNearLocation($latitude, $longitude, $radiusInKm);
    }

    /**
     * Find restaurants by cuisine type.
     *
     * @param string $cuisineType
     * @return Collection
     */
    public function findByCuisineType(string $cuisineType): Collection
    {
        return $this->restaurantRepository->findByCuisineType($cuisineType);
    }

    /**
     * Find restaurants by price range.
     *
     * @param float $minPrice
     * @param float $maxPrice
     * @return Collection
     */
    public function findByPriceRange(float $minPrice, float $maxPrice): Collection
    {
        return $this->restaurantRepository->findByPriceRange($minPrice, $maxPrice);
    }

    /**
     * Find restaurants by minimum rating.
     *
     * @param float $minRating
     * @return Collection
     */
    public function findByMinimumRating(float $minRating): Collection
    {
        return $this->restaurantRepository->findByMinimumRating($minRating);
    }
}
