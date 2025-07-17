<?php

declare(strict_types=1);

namespace App\CQRS\CommandHandlers;

use App\CQRS\Commands\CommandInterface;

interface CommandHandlerInterface
{
    /**
     * Handle a command
     * 
     * @param CommandInterface $command
     * @return mixed
     */
    public function handle(CommandInterface $command);
}
