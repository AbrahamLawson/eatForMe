<?php

declare(strict_types=1);

namespace App\CQRS\Queries;

use DateTime;

final readonly class FindPotentialMatchesQuery implements QueryInterface
{
    /**
     * FindPotentialMatchesQuery constructor.
     *
     * @param int $userId
     * @param float $latitude
     * @param float $longitude
     * @param float $radius
     * @param DateTime|null $startTime
     * @param DateTime|null $endTime
     * @param array|null $preferences
     */
    public function __construct(
        private readonly int $userId,
        private readonly float $latitude,
        private readonly float $longitude,
        private readonly float $radius,
        private readonly ?DateTime $startTime = null,
        private readonly ?DateTime $endTime = null,
        private readonly ?array $preferences = null
    ) {
    }

    /**
     * Get the user ID.
     *
     * @return int
     */
    public function getUserId(): int
    {
        return $this->userId;
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
     * Get the start time.
     *
     * @return DateTime|null
     */
    public function getStartTime(): ?DateTime
    {
        return $this->startTime;
    }

    /**
     * Get the end time.
     *
     * @return DateTime|null
     */
    public function getEndTime(): ?DateTime
    {
        return $this->endTime;
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
}
