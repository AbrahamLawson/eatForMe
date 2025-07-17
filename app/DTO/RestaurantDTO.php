<?php

declare(strict_types=1);

namespace App\DTO;

final readonly class RestaurantDTO
{
    /**
     * RestaurantDTO constructor.
     *
     * @param string $name
     * @param string $address
     * @param float $latitude
     * @param float $longitude
     * @param array $cuisineTypes
     * @param float|null $rating
     * @param string|null $priceRange
     * @param string|null $phoneNumber
     * @param string|null $website
     * @param array|null $openingHours
     * @param array|null $photos
     */
    public function __construct(
        private string $name,
        private string $address,
        private float $latitude,
        private float $longitude,
        private array $cuisineTypes,
        private ?float $rating = null,
        private ?string $priceRange = null,
        private ?string $phoneNumber = null,
        private ?string $website = null,
        private ?array $openingHours = null,
        private ?array $photos = null
    ) {
    }

    /**
     * Get the name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the address.
     *
     * @return string
     */
    public function getAddress(): string
    {
        return $this->address;
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
     * Get the cuisine types.
     *
     * @return array
     */
    public function getCuisineTypes(): array
    {
        return $this->cuisineTypes;
    }

    /**
     * Get the rating.
     *
     * @return float|null
     */
    public function getRating(): ?float
    {
        return $this->rating;
    }

    /**
     * Get the price range.
     *
     * @return string|null
     */
    public function getPriceRange(): ?string
    {
        return $this->priceRange;
    }

    /**
     * Get the phone number.
     *
     * @return string|null
     */
    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    /**
     * Get the website.
     *
     * @return string|null
     */
    public function getWebsite(): ?string
    {
        return $this->website;
    }

    /**
     * Get the opening hours.
     *
     * @return array|null
     */
    public function getOpeningHours(): ?array
    {
        return $this->openingHours;
    }

    /**
     * Get the photos.
     *
     * @return array|null
     */
    public function getPhotos(): ?array
    {
        return $this->photos;
    }

    /**
     * Create from array.
     *
     * @param array $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            $data['name'],
            $data['address'],
            $data['latitude'],
            $data['longitude'],
            $data['cuisine_types'],
            $data['rating'] ?? null,
            $data['price_range'] ?? null,
            $data['phone_number'] ?? null,
            $data['website'] ?? null,
            $data['opening_hours'] ?? null,
            $data['photos'] ?? null
        );
    }

    /**
     * Convert to array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'address' => $this->address,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'cuisine_types' => $this->cuisineTypes,
            'rating' => $this->rating,
            'price_range' => $this->priceRange,
            'phone_number' => $this->phoneNumber,
            'website' => $this->website,
            'opening_hours' => $this->openingHours,
            'photos' => $this->photos,
        ];
    }
}
