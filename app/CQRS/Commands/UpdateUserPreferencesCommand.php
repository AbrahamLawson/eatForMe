<?php

declare(strict_types=1);

namespace App\CQRS\Commands;

use App\DTO\UserPreferencesDTO;

final readonly class UpdateUserPreferencesCommand
{
    /**
     * UpdateUserPreferencesCommand constructor.
     *
     * @param int $userId
     * @param UserPreferencesDTO $preferences
     */
    public function __construct(
        public int $userId,
        public UserPreferencesDTO $preferences,
    ) {
    }
}
