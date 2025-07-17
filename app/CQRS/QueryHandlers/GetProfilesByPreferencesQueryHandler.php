<?php

declare(strict_types=1);

namespace App\CQRS\QueryHandlers;

use App\CQRS\Queries\GetProfilesByPreferencesQuery;
use App\CQRS\Queries\QueryInterface;
use App\Interfaces\ProfileRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use InvalidArgumentException;

final readonly class GetProfilesByPreferencesQueryHandler implements QueryHandlerInterface
{
    /**
     * GetProfilesByPreferencesQueryHandler constructor.
     *
     * @param ProfileRepositoryInterface $profileRepository
     */
    public function __construct(
        private ProfileRepositoryInterface $profileRepository
    ) {
    }

    /**
     * Handle the query.
     *
     * @param QueryInterface $query
     * @return Collection
     * @throws InvalidArgumentException
     */
    public function handle(QueryInterface $query): Collection
    {
        if (!$query instanceof GetProfilesByPreferencesQuery) {
            throw new InvalidArgumentException('Query must be an instance of GetProfilesByPreferencesQuery');
        }

        return $this->profileRepository->findByPreferences(
            $query->getPreferences(),
            $query->getLimit(),
            $query->getOffset()
        );
    }
}
