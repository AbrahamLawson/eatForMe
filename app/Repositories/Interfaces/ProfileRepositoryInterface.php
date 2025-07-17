<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use App\DTO\ProfileDTO;

interface ProfileRepositoryInterface extends RepositoryInterface
{
    /**
     * Crée un nouveau profil
     *
     * @param ProfileDTO $profileDTO
     * @return ProfileDTO
     */
    public function createFromDTO(ProfileDTO $profileDTO): ProfileDTO;
    
    /**
     * Find profile by user ID
     * 
     * @param int $userId
     * @return \App\Models\Profile|null
     */
    public function findByUserId(int $userId);
}
