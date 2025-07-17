<?php

declare(strict_types=1);

namespace App\DTO;

final readonly class ProfileDTO
{
    /**
     * ProfileDTO constructor.
     * 
     * @param string|null $bio
     * @param array $preferences
     * @param array $dietaryRestrictions
     * @param array $favoriteCuisines
     * @param string|null $avatarUrl
     */
    public function __construct(
        private ?string $bio = null,
        private array $preferences = [],
        private array $dietaryRestrictions = [],
        private array $favoriteCuisines = [],
        private ?string $avatarUrl = null
    ) {
    }

    /**
     * Create from array
     * 
     * @param array $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            $data['bio'] ?? null,
            $data['preferences'] ?? [],
            $data['dietary_restrictions'] ?? [],
            $data['favorite_cuisines'] ?? [],
            $data['avatar_url'] ?? null
        );
    }

    /**
     * Convert to array
     * 
     * @return array
     */
    public function toArray(): array
    {
        return [
            'bio' => $this->bio,
            'preferences' => $this->preferences,
            'dietary_restrictions' => $this->dietaryRestrictions,
            'favorite_cuisines' => $this->favoriteCuisines,
            'avatar_url' => $this->avatarUrl,
        ];
    }

    /**
     * Get the bio.
     *
     * @return string|null
     */
    public function getBio(): ?string
    {
        return $this->bio;
    }

    /**
     * Get the preferences.
     *
     * @return array
     */
    public function getPreferences(): array
    {
        return $this->preferences;
    }

    /**
     * Get the dietary restrictions.
     *
     * @return array
     */
    public function getDietaryRestrictions(): array
    {
        return $this->dietaryRestrictions;
    }

    /**
     * Get the favorite cuisines.
     *
     * @return array
     */
    public function getFavoriteCuisines(): array
    {
        return $this->favoriteCuisines;
    }

    /**
     * Get the avatar URL.
     *
     * @return string|null
     */
    public function getAvatarUrl(): ?string
    {
        return $this->avatarUrl;
    }
}
