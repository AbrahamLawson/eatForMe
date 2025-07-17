<?php

declare(strict_types=1);

namespace App\CQRS\Commands;

use App\DTO\ProfileDTO;

final readonly class UpdateProfileCommand implements CommandInterface
{
    /**
     * UpdateProfileCommand constructor.
     *
     * @param int $userId
     * @param ProfileDTO $profileDTO
     */
    public function __construct(
        private readonly int $userId,
        private readonly ProfileDTO $profileDTO
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
     * Get the profile DTO.
     *
     * @return ProfileDTO
     */
    public function getProfileDTO(): ProfileDTO
    {
        return $this->profileDTO;
    }
}
