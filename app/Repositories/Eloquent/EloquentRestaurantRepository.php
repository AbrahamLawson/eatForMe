<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\DTO\RestaurantDTO;
use App\Models\Restaurant;
use App\Repositories\Interfaces\RestaurantRepositoryInterface;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

final class EloquentRestaurantRepository extends AbstractEloquentRepository implements RestaurantRepositoryInterface
{
    /**
     * @param Restaurant $restaurant
     */
    public function __construct(
        private Restaurant $restaurant
    ) {
        $this->model = $restaurant;
    }

    /**
     * Trouve des restaurants par localisation
     *
     * @param float $latitude
     * @param float $longitude
     * @param float $radius Distance en kilomètres
     * @param array $filters Filtres optionnels (cuisine, prix, etc.)
     * @return EloquentCollection
     */
    public function findByLocation(float $latitude, float $longitude, float $radius = 5.0, array $filters = []): EloquentCollection
    {
        // Calcul de la distance avec la formule Haversine
        $query = $this->restaurant
            ->select('*')
            ->selectRaw(
                '(6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance',
                [$latitude, $longitude, $latitude]
            )
            ->having('distance', '<=', $radius)
            ->orderBy('distance');
        
        // Appliquer les filtres
        if (isset($filters['cuisine']) && !empty($filters['cuisine'])) {
            $query->where('cuisine_type', $filters['cuisine']);
        }
        
        if (isset($filters['price_range']) && !empty($filters['price_range'])) {
            $query->where('price_range', $filters['price_range']);
        }
        
        if (isset($filters['rating']) && !empty($filters['rating'])) {
            $query->where('average_rating', '>=', $filters['rating']);
        }
        
        $restaurants = $query->get();
        
        return $restaurants->map(function ($restaurant) {
            return RestaurantDTO::fromArray($restaurant->toArray());
        });
    }

    /**
     * Trouve un restaurant par son ID
     *
     * @param int $id
     * @return RestaurantDTO|null
     */
    public function findById(int $id): ?RestaurantDTO
    {
        $restaurant = $this->restaurant->find($id);
        
        if (!$restaurant) {
            return null;
        }
        
        return RestaurantDTO::fromArray($restaurant->toArray());
    }
}
