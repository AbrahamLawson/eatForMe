<?php

declare(strict_types=1);

namespace App\CQRS\QueryHandlers;

use App\CQRS\Queries\GetConversationQuery;
use App\CQRS\Queries\QueryInterface;
use App\Interfaces\MessageRepositoryInterface;
use App\Interfaces\UserMatchRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use InvalidArgumentException;

final readonly class GetConversationQueryHandler implements QueryHandlerInterface
{
    /**
     * GetConversationQueryHandler constructor.
     *
     * @param MessageRepositoryInterface $messageRepository
     * @param UserMatchRepositoryInterface $userMatchRepository
     */
    public function __construct(
        private MessageRepositoryInterface $messageRepository,
        private UserMatchRepositoryInterface $userMatchRepository
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
        if (!$query instanceof GetConversationQuery) {
            throw new InvalidArgumentException('Query must be an instance of GetConversationQuery');
        }

        // Verify that the match exists
        $match = $this->userMatchRepository->findById($query->getMatchId());
        if (!$match) {
            throw new InvalidArgumentException("Match with ID {$query->getMatchId()} not found");
        }

        return $this->messageRepository->findByMatchId(
            $query->getMatchId(),
            $query->getLimit(),
            $query->getOffset()
        );
    }
}
