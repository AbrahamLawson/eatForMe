<?php

declare(strict_types=1);

namespace App\CQRS\CommandHandlers;

use App\CQRS\Commands\CommandInterface;
use App\CQRS\Commands\UpdateProfileCommand;
use App\Interfaces\ProfileRepositoryInterface;
use App\Models\Profile;
use InvalidArgumentException;

final readonly class UpdateProfileCommandHandler implements CommandHandlerInterface
{
    /**
     * UpdateProfileCommandHandler constructor.
     *
     * @param ProfileRepositoryInterface $profileRepository
     */
    public function __construct(
        private ProfileRepositoryInterface $profileRepository
    ) {
    }

    /**
     * Handle the command.
     *
     * @param CommandInterface $command
     * @return Profile
     * @throws InvalidArgumentException
     */
    public function handle(CommandInterface $command): Profile
    {
        if (!$command instanceof UpdateProfileCommand) {
            throw new InvalidArgumentException('Command must be an instance of UpdateProfileCommand');
        }

        $userId = $command->getUserId();
        $profileDTO = $command->getProfileDTO();

        // Get the profile
        $profile = $this->profileRepository->findByUserId($userId);
        if (!$profile) {
            throw new InvalidArgumentException("Profile for user with ID {$userId} not found");
        }

        // Update profile data
        $profileData = [
            'bio' => $profileDTO->getBio(),
            'preferences' => $profileDTO->getPreferences(),
            'dietary_restrictions' => $profileDTO->getDietaryRestrictions(),
            'favorite_cuisines' => $profileDTO->getFavoriteCuisines(),
            'avatar_url' => $profileDTO->getAvatarUrl()
        ];

        return $this->profileRepository->update($profile->id, $profileData);
    }
}
