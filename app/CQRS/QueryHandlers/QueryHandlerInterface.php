<?php

declare(strict_types=1);

namespace App\CQRS\QueryHandlers;

use App\CQRS\Queries\QueryInterface;

interface QueryHandlerInterface
{
    /**
     * Handle a query
     * 
     * @param QueryInterface $query
     * @return mixed
     */
    public function handle(QueryInterface $query);
}
