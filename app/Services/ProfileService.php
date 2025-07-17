<?php

declare(strict_types=1);

namespace App\Services;

use App\CQRS\CommandHandlers\CreateProfileCommandHandler;
use App\CQRS\Commands\CreateProfileCommand;
use App\CQRS\Queries\GetProfileByUserIdQuery;
use App\CQRS\QueryHandlers\GetProfileByUserIdQueryHandler;
use App\DTO\ProfileDTO;

class ProfileService
{
    /**
     * @var CreateProfileCommandHandler
     */
    private $createProfileCommandHandler;

    /**
     * @var GetProfileByUserIdQueryHandler
     */
    private $getProfileByUserIdQueryHandler;

    /**
     * ProfileService constructor.
     * 
     * @param CreateProfileCommandHandler $createProfileCommandHandler
     * @param GetProfileByUserIdQueryHandler $getProfileByUserIdQueryHandler
     */
    public function __construct(
        CreateProfileCommandHandler $createProfileCommandHandler,
        GetProfileByUserIdQueryHandler $getProfileByUserIdQueryHandler
    ) {
        $this->createProfileCommandHandler = $createProfileCommandHandler;
        $this->getProfileByUserIdQueryHandler = $getProfileByUserIdQueryHandler;
    }

    /**
     * Create or update user profile
     * 
     * @param array $data
     * @return \App\Models\Profile
     */
    public function createOrUpdateProfile(array $data)
    {
        $profileDTO = ProfileDTO::fromArray($data);
        $command = new CreateProfileCommand($profileDTO);
        
        return $this->createProfileCommandHandler->handle($command);
    }

    /**
     * Get user profile
     * 
     * @param int $userId
     * @return \App\Models\Profile|null
     */
    public function getProfile(int $userId)
    {
        $query = new GetProfileByUserIdQuery($userId);
        
        return $this->getProfileByUserIdQueryHandler->handle($query);
    }
}
