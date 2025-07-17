<?php

declare(strict_types=1);

namespace App\CQRS\Queries;

final readonly class FindRestaurantsByLocationQuery implements QueryInterface
{
    /**
     * FindRestaurantsByLocationQuery constructor.
     *
     * @param float $latitude
     * @param float $longitude
     * @param float $radius
     * @param array|null $cuisineTypes
     * @param array|null $priceRanges
     * @param int|null $limit
     * @param int|null $offset
     */
    public function __construct(
        private float $latitude,
        private float $longitude,
        private float $radius,
        private ?array $cuisineTypes = null,
        private ?array $priceRanges = null,
        private ?int $limit = 20,
        private ?int $offset = 0
    ) {
    }

    /**
     * Get the latitude.
     *
     * @return float
     */
    public function getLatitude(): float
    {
        return $this->latitude;
    }

    /**
     * Get the longitude.
     *
     * @return float
     */
    public function getLongitude(): float
    {
        return $this->longitude;
    }

    /**
     * Get the radius.
     *
     * @return float
     */
    public function getRadius(): float
    {
        return $this->radius;
    }

    /**
     * Get the cuisine types.
     *
     * @return array|null
     */
    public function getCuisineTypes(): ?array
    {
        return $this->cuisineTypes;
    }

    /**
     * Get the price ranges.
     *
     * @return array|null
     */
    public function getPriceRanges(): ?array
    {
        return $this->priceRanges;
    }

    /**
     * Get the limit.
     *
     * @return int|null
     */
    public function getLimit(): ?int
    {
        return $this->limit;
    }

    /**
     * Get the offset.
     *
     * @return int|null
     */
    public function getOffset(): ?int
    {
        return $this->offset;
    }
}
