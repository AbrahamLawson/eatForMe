<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\UserMatch;
use App\Repositories\Interfaces\UserMatchRepositoryInterface;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model;

final class EloquentMatchRepository extends AbstractEloquentRepository implements UserMatchRepositoryInterface
{
    /**
     * @param UserMatch $userMatch
     */
    public function __construct(
        private UserMatch $userMatch
    ) {
        $this->model = $userMatch;
    }

    /**
     * Crée un nouveau match entre deux disponibilités
     *
     * @param int $initiatorAvailabilityId
     * @param int $receiverAvailabilityId
     * @return int ID du match créé
     */
    public function createMatch(int $initiatorAvailabilityId, int $receiverAvailabilityId): int
    {
        $match = $this->userMatch->create([
            'initiator_availability_id' => $initiatorAvailabilityId,
            'receiver_availability_id' => $receiverAvailabilityId,
            'status' => 'pending',
            'matched_at' => now(),
        ]);

        return $match->id;
    }

    /**
     * Trouve un match par son ID
     *
     * @param int $id
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function findById(int $id): ?\Illuminate\Database\Eloquent\Model
    {
        return $this->userMatch->with(['initiatorAvailability.user', 'receiverAvailability.user'])->find($id);
    }
    
    /**
     * Trouve un match par son ID et retourne un tableau formaté
     *
     * @param int $id
     * @return array|null
     */
    public function findByIdAsArray(int $id): ?array
    {
        $match = $this->findById($id);
        
        if (!$match) {
            return null;
        }
        
        return [
            'id' => $match->id,
            'initiator_availability_id' => $match->initiator_availability_id,
            'receiver_availability_id' => $match->receiver_availability_id,
            'status' => $match->status,
            'matched_at' => $match->matched_at,
            'initiator' => $match->initiatorAvailability->user->toArray(),
            'receiver' => $match->receiverAvailability->user->toArray(),
        ];
    }

    /**
     * Trouve les matches initiés par un utilisateur
     *
     * @param int $userId
     * @return EloquentCollection
     */
    public function findByUserId(int $userId): EloquentCollection
    {
        return $this->userMatch
            ->with(['initiatorAvailability.user', 'receiverAvailability.user'])
            ->whereHas('initiatorAvailability', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->get();
    }
    
    /**
     * Trouve les matches reçus par un utilisateur
     *
     * @param int $userId
     * @return EloquentCollection
     */
    public function findByMatchedUserId(int $userId): EloquentCollection
    {
        return $this->userMatch
            ->with(['initiatorAvailability.user', 'receiverAvailability.user'])
            ->whereHas('receiverAvailability', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->get();
    }
    
    /**
     * Trouve tous les matches d'un utilisateur (initiés et reçus)
     *
     * @param int $userId
     * @return EloquentCollection
     */
    public function findAllForUser(int $userId): EloquentCollection
    {
        return $this->userMatch
            ->with(['initiatorAvailability.user', 'receiverAvailability.user'])
            ->whereHas('initiatorAvailability', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->orWhereHas('receiverAvailability', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->get();
    }
    
    /**
     * Trouve les matches par ID de disponibilité
     *
     * @param int $availabilityId
     * @return EloquentCollection
     */
    public function findByAvailabilityId(int $availabilityId): EloquentCollection
    {
        return $this->userMatch
            ->with(['initiatorAvailability.user', 'receiverAvailability.user'])
            ->where('initiator_availability_id', $availabilityId)
            ->orWhere('receiver_availability_id', $availabilityId)
            ->get();
    }
    
    /**
     * Trouve les matches par statut
     *
     * @param string $status
     * @return EloquentCollection
     */
    public function findByStatus(string $status): EloquentCollection
    {
        return $this->userMatch
            ->with(['initiatorAvailability.user', 'receiverAvailability.user'])
            ->where('status', $status)
            ->get();
    }
}
