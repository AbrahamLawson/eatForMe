<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use App\DTO\AvailabilityDTO;
use App\Models\Availability;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;

interface AvailabilityRepositoryInterface extends RepositoryInterface
{
    /**
     * Create a new availability from DTO
     *
     * @param AvailabilityDTO $availabilityDTO
     * @return AvailabilityDTO
     */
    public function createFromDTO(AvailabilityDTO $availabilityDTO): AvailabilityDTO;
    
    /**
     * Find availability by ID and return as DTO
     *
     * @param int $id
     * @return AvailabilityDTO|null
     */
    public function findByIdAsDTO(int $id): ?AvailabilityDTO;
    
    /**
     * Find availabilities by user ID.
     *
     * @param int $userId
     * @return EloquentCollection
     */
    public function findByUserId(int $userId): EloquentCollection;
    
    /**
     * Find active availabilities within a specific time range.
     *
     * @param string $startTime
     * @param string $endTime
     * @return EloquentCollection
     */
    public function findActiveInTimeRange(string $startTime, string $endTime): EloquentCollection;
    
    /**
     * Find availabilities near a specific location.
     *
     * @param float $latitude
     * @param float $longitude
     * @param float $radiusInKm
     * @return EloquentCollection
     */
    public function findNearLocation(float $latitude, float $longitude, float $radiusInKm = 5.0): EloquentCollection;
    
    /**
     * Récupère les disponibilités filtrées par préférences utilisateur.
     *
     * @param int $userId
     * @param array|null $userPreferences
     * @param array $filters
     * @return EloquentCollection
     */
    public function findByUserPreferences(int $userId, ?array $userPreferences, array $filters = []): EloquentCollection;
}
