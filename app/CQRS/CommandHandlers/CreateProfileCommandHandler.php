<?php

declare(strict_types=1);

namespace App\CQRS\CommandHandlers;

use App\CQRS\Commands\CommandInterface;
use App\CQRS\Commands\CreateProfileCommand;
use App\Repositories\ProfileRepositoryInterface;
use SebastianBergmann\Type\MixedType;

class CreateProfileCommandHandler implements CommandHandlerInterface
{
    /**
     * CreateProfileCommandHandler constructor.
     *
     * @param ProfileRepositoryInterface $profileRepository
     */
    public function __construct(private ProfileRepositoryInterface $profileRepository){}

    /**
     * Handle a command
     *
     * @param CommandInterface $command
     * @return mixed
     * @throws \InvalidArgumentException
     */
    public function handle(CommandInterface $command): MixedType
    {
        if (!$command instanceof CreateProfileCommand) {
            throw new \InvalidArgumentException('Command must be an instance of CreateProfileCommand');
        }

        $profileDTO = $command->getProfileDTO();

        return $this->profileRepository->createFromDTO($profileDTO);
    }
}
