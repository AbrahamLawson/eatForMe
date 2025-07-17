<?php

declare(strict_types=1);

namespace App\CQRS\Queries;

final readonly class FindNearbyUsersQuery implements QueryInterface
{
    /**
     * FindNearbyUsersQuery constructor.
     *
     * @param int $userId L'ID de l'utilisateur qui effectue la recherche
     * @param float $latitude La latitude de la position de l'utilisateur
     * @param float $longitude La longitude de la position de l'utilisateur
     * @param float $distance La distance maximale de recherche en kilomètres
     * @param string $activity Le type d'activité recherchée (eat, drink, chat)
     * @param string $profile Le type de profil recherché (any, male, female)
     */
    public function __construct(
        private int $userId,
        private float $latitude,
        private float $longitude,
        private float $distance,
        private string $activity,
        private string $profile
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
     * Get the distance.
     *
     * @return float
     */
    public function getDistance(): float
    {
        return $this->distance;
    }

    /**
     * Get the activity.
     *
     * @return string
     */
    public function getActivity(): string
    {
        return $this->activity;
    }

    /**
     * Get the profile.
     *
     * @return string
     */
    public function getProfile(): string
    {
        return $this->profile;
    }
}
