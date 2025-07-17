<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use App\Models\Restaurant;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;

interface RestaurantRepositoryInterface extends RepositoryInterface
{
    /**
     * Find restaurants by name (partial match).
     *
     * @param string $name
     * @return EloquentCollection
     */
    public function findByName(string $name): EloquentCollection;
    
    /**
     * Find restaurants near a specific location.
     *
     * @param float $latitude
     * @param float $longitude
     * @param float $radiusInKm
     * @return EloquentCollection
     */
    public function findNearLocation(float $latitude, float $longitude, float $radiusInKm = 5.0): EloquentCollection;
    
    /**
     * Find restaurants by cuisine type.
     *
     * @param string $cuisineType
     * @return EloquentCollection
     */
    public function findByCuisineType(string $cuisineType): EloquentCollection;
    
    /**
     * Find restaurants by price range.
     *
     * @param float $minPrice
     * @param float $maxPrice
     * @return EloquentCollection
     */
    public function findByPriceRange(float $minPrice, float $maxPrice): EloquentCollection;
    
    /**
     * Find restaurants by minimum rating.
     *
     * @param float $minRating
     * @return EloquentCollection
     */
    public function findByMinimumRating(float $minRating): EloquentCollection;
}
