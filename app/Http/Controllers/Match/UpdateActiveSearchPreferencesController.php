<?php

declare(strict_types=1);

namespace App\Http\Controllers\Match;

use App\CQRS\CommandHandlers\UpdateUserPreferencesCommandHandler;
use App\CQRS\Commands\UpdateUserPreferencesCommand;
use App\DTO\UserPreferencesDTO;
use App\Http\Requests\UpdateUserPreferencesRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;

final readonly class UpdateActiveSearchPreferencesController
{
    /**
     * UpdateActiveSearchPreferencesController constructor.
     *
     * @param UpdateUserPreferencesCommandHandler $handler
     */
    public function __construct(
        private readonly UpdateUserPreferencesCommandHandler $handler
    ) {
    }

    /**
     * Handle the incoming request to update active search preferences.
     *
     * @param UpdateUserPreferencesRequest $request
     * @return RedirectResponse
     */
    public function __invoke(UpdateUserPreferencesRequest $request): RedirectResponse
    {
        $userId = $request->user()->id;
        $preferencesDTO = UserPreferencesDTO::fromArray($request->validated());

        $command = new UpdateUserPreferencesCommand($userId, $preferencesDTO);
        $this->handler->handle($command);

        return Redirect::to('/active-search')->with('status', 'preferences-updated');
    }
}
