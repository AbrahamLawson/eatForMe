<?php

declare(strict_types=1);

namespace App\CQRS\QueryHandlers;

use App\CQRS\Queries\GetProfileByUserIdQuery;
use App\CQRS\Queries\QueryInterface;
use App\Interfaces\ProfileRepositoryInterface;

class GetProfileByUserIdQueryHandler implements QueryHandlerInterface
{
    /**
     * @var ProfileRepositoryInterface
     */
    private $profileRepository;

    /**
     * GetProfileByUserIdQueryHandler constructor.
     * 
     * @param ProfileRepositoryInterface $profileRepository
     */
    public function __construct(ProfileRepositoryInterface $profileRepository)
    {
        $this->profileRepository = $profileRepository;
    }

    /**
     * Handle a query
     * 
     * @param QueryInterface $query
     * @return mixed
     * @throws \InvalidArgumentException
     */
    public function handle(QueryInterface $query)
    {
        if (!$query instanceof GetProfileByUserIdQuery) {
            throw new \InvalidArgumentException('Query must be an instance of GetProfileByUserIdQuery');
        }

        return $this->profileRepository->findByUserId($query->getUserId());
    }
}
