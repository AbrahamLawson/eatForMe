<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use App\Models\UserMatch;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;

interface UserMatchRepositoryInterface extends RepositoryInterface
{
    /**
     * Crée un nouveau match entre deux disponibilités
     *
     * @param int $initiatorAvailabilityId
     * @param int $receiverAvailabilityId
     * @return int ID du match créé
     */
    public function createMatch(int $initiatorAvailabilityId, int $receiverAvailabilityId): int;
    
    /**
     * Find matches by user ID (matches initiated by the user).
     *
     * @param int $userId
     * @return EloquentCollection
     */
    public function findByUserId(int $userId): EloquentCollection;
    
    /**
     * Find matches where the user is the matched user (matches received by the user).
     *
     * @param int $userId
     * @return EloquentCollection
     */
    public function findByMatchedUserId(int $userId): EloquentCollection;
    
    /**
     * Find all matches for a user (both initiated and received).
     *
     * @param int $userId
     * @return EloquentCollection
     */
    public function findAllForUser(int $userId): EloquentCollection;
    
    /**
     * Find matches by availability ID.
     *
     * @param int $availabilityId
     * @return EloquentCollection
     */
    public function findByAvailabilityId(int $availabilityId): EloquentCollection;
    
    /**
     * Find matches by status.
     *
     * @param string $status
     * @return EloquentCollection
     */
    public function findByStatus(string $status): EloquentCollection;
}
