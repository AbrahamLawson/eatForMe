<?php

declare(strict_types=1);

namespace App\CQRS\Commands;

use App\DTO\ProfileDTO;

class CreateProfileCommand implements CommandInterface
{
    /**
     * CreateProfileCommand constructor.
     *
     * @param ProfileDTO $profileDTO
     */
    public function __construct(private ProfileDTO $profileDTO){}

    /**
     * Get profile DTO
     *
     * @return ProfileDTO
     */
    public function getProfileDTO(): ProfileDTO
    {
        return $this->profileDTO;
    }
}
