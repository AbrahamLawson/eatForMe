<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Models\User;
use App\Repositories\Eloquent\EloquentUserRepository;

final class UserRepository implements UserRepositoryInterface
{
    /**
     * @var EloquentUserRepository
     */
    private $eloquentRepository;

    /**
     * UserRepository constructor.
     * 
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->eloquentRepository = new EloquentUserRepository($user);
    }

    /**
     * Get all resources
     * 
     * @param array $columns
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll(array $columns = ['*'])
    {
        return $this->eloquentRepository->getAll($columns);
    }

    /**
     * Get paginated resources
     * 
     * @param int $perPage
     * @param array $columns
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getPaginated(int $perPage = 15, array $columns = ['*'])
    {
        return $this->eloquentRepository->getPaginated($perPage, $columns);
    }

    /**
     * Find resource by id
     * 
     * @param int $id
     * @return mixed
     */
    public function findById(int $id)
    {
        return $this->eloquentRepository->findById($id);
    }

    /**
     * Create new resource
     * 
     * @param array $attributes
     * @return mixed
     */
    public function create(array $attributes)
    {
        return $this->eloquentRepository->create($attributes);
    }

    /**
     * Update resource
     * 
     * @param int $id
     * @param array $attributes
     * @return bool
     */
    public function update(int $id, array $attributes): bool
    {
        return $this->eloquentRepository->update($id, $attributes);
    }

    /**
     * Delete resource
     * 
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        return $this->eloquentRepository->delete($id);
    }

    /**
     * Find user by email
     * 
     * @param string $email
     * @return User|null
     */
    public function findByEmail(string $email)
    {
        return $this->eloquentRepository->findByEmail($email);
    }
    
    /**
     * Get users by location proximity
     * 
     * @param float $latitude
     * @param float $longitude
     * @param float $radius in kilometers
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function findByLocation(float $latitude, float $longitude, float $radius = 10)
    {
        return $this->eloquentRepository->findByLocation($latitude, $longitude, $radius);
    }
    
    /**
     * Find user with preferences and active availabilities
     * 
     * @param int $userId
     * @return User
     */
    public function findUserWithPreferencesAndAvailabilities(int $userId)
    {
        return $this->eloquentRepository->findUserWithPreferencesAndAvailabilities($userId);
    }
}
