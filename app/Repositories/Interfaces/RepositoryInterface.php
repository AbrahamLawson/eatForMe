<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

interface RepositoryInterface
{
    /**
     * Get all resources
     * 
     * @param array $columns
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll(array $columns = ['*']);

    /**
     * Get paginated resources
     * 
     * @param int $perPage
     * @param array $columns
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getPaginated(int $perPage = 15, array $columns = ['*']);

    /**
     * Find resource by id
     * 
     * @param int $id
     * @return mixed
     */
    public function findById(int $id);

    /**
     * Create new resource
     * 
     * @param array $attributes
     * @return mixed
     */
    public function create(array $attributes);

    /**
     * Update resource
     * 
     * @param int $id
     * @param array $attributes
     * @return bool
     */
    public function update(int $id, array $attributes): bool;

    /**
     * Delete resource
     * 
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool;
}
