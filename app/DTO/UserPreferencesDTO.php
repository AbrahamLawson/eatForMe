<?php

declare(strict_types=1);

namespace App\DTO;

final readonly class UserPreferencesDTO
{
    /**
     * @param string $genderPreference
     * @param int $ageRangeMin
     * @param int $ageRangeMax
     * @param array<string> $interests
     * @param string|null $paymentPreference
     */
    public function __construct(
        public string $genderPreference,
        public int $ageRangeMin,
        public int $ageRangeMax,
        public array $interests,
        public ?string $paymentPreference = null,
    ) {
    }

    /**
     * Create a DTO from an array.
     *
     * @param array<string, mixed> $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            genderPreference: $data['gender_preference'],
            ageRangeMin: (int) $data['age_range_min'],
            ageRangeMax: (int) $data['age_range_max'],
            interests: $data['interests'] ?? [],
            paymentPreference: $data['payment_preference'] ?? null,
        );
    }

    /**
     * Convert the DTO to an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'gender_preference' => $this->genderPreference,
            'age_range_min' => $this->ageRangeMin,
            'age_range_max' => $this->ageRangeMax,
            'interests' => $this->interests,
            'payment_preference' => $this->paymentPreference,
        ];
    }
}
