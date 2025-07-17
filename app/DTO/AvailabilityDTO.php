<?php

declare(strict_types=1);

namespace App\DTO;

final readonly class AvailabilityDTO
{
    /**
     * Create from array
     * 
     * @param array $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            $data['latitude'] ?? 0.0,
            $data['longitude'] ?? 0.0,
            $data['preferences'] ? (is_string($data['preferences']) ? json_decode($data['preferences'], true) : $data['preferences']) : null,
            $data['is_active'] ?? true
        );
    }

    /**
     * AvailabilityDTO constructor.
     *
     * @param float $locationLat
     * @param float $locationLng
     * @param array|null $preferences
     * @param bool $isActive
     */
    public function __construct(
        private float $locationLat,
        private float $locationLng,
        private ?array $preferences = null,
        private bool $isActive = true
    ) {
    }

    /**
     * Get the location latitude.
     *
     * @return float
     */
    public function getLocationLat(): float
    {
        return $this->locationLat;
    }

    /**
     * Get the location longitude.
     *
     * @return float
     */
    public function getLocationLng(): float
    {
        return $this->locationLng;
    }

    /**
     * Get the preferences.
     *
     * @return array|null
     */
    public function getPreferences(): ?array
    {
        return $this->preferences;
    }

    /**
     * Check if the availability is active.
     *
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->isActive;
    }
}
