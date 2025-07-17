<?php

declare(strict_types=1);

namespace App\CQRS\Queries;

class GetProfileByUserIdQuery implements QueryInterface
{
    /**
     * @var int
     */
    private $userId;

    /**
     * GetProfileByUserIdQuery constructor.
     * 
     * @param int $userId
     */
    public function __construct(int $userId)
    {
        $this->userId = $userId;
    }

    /**
     * Get user ID
     * 
     * @return int
     */
    public function getUserId(): int
    {
        return $this->userId;
    }
}
