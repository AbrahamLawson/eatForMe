<?php

declare(strict_types=1);

namespace App\CQRS\CommandHandlers;

use App\CQRS\Commands\UpdateUserPreferencesCommand;
use App\Models\User;
use Illuminate\Support\Facades\Log;

final class UpdateUserPreferencesCommandHandler
{
    /**
     * Handle the update user preferences command.
     *
     * @param UpdateUserPreferencesCommand $command
     * @return void
     */
    public function handle(UpdateUserPreferencesCommand $command): void
    {
        try {
            $user = User::findOrFail($command->userId);
            $user->preferences = $command->preferences->toArray();
            $user->save();
            
            Log::info('User preferences updated', [
                'user_id' => $command->userId,
                'preferences' => $command->preferences->toArray(),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to update user preferences', [
                'user_id' => $command->userId,
                'error' => $e->getMessage(),
            ]);
            
            throw $e;
        }
    }
}
