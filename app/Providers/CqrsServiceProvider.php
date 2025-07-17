<?php

declare(strict_types=1);

namespace App\Providers;

use App\CQRS\CommandHandlers\CreateAvailabilityCommandHandler;
use App\CQRS\CommandHandlers\CreateMatchCommandHandler;
use App\CQRS\CommandHandlers\CreateProfileCommandHandler;
use App\CQRS\CommandHandlers\MarkMessageAsReadCommandHandler;
use App\CQRS\CommandHandlers\SendMessageCommandHandler;
use App\CQRS\CommandHandlers\UpdateProfileCommandHandler;
use App\CQRS\QueryHandlers\FindRestaurantsByLocationQueryHandler;
use App\CQRS\QueryHandlers\GetConversationQueryHandler;
use App\CQRS\QueryHandlers\GetProfilesByPreferencesQueryHandler;
use App\Repositories\Interfaces\AvailabilityRepositoryInterface;
use App\Repositories\Interfaces\UserMatchRepositoryInterface as MatchRepositoryInterface;
use App\Repositories\Interfaces\MessageRepositoryInterface;
use App\Repositories\Interfaces\ProfileRepositoryInterface;
use App\Repositories\Interfaces\RestaurantRepositoryInterface;
use App\Repositories\Eloquent\EloquentAvailabilityRepository;
use App\Repositories\Eloquent\EloquentMatchRepository;
use App\Repositories\Eloquent\EloquentMessageRepository;
use App\Repositories\Eloquent\EloquentProfileRepository;
use App\Repositories\Eloquent\EloquentRestaurantRepository;
use Illuminate\Support\ServiceProvider;

class CqrsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Repositories
        $this->app->bind(ProfileRepositoryInterface::class, EloquentProfileRepository::class);
        $this->app->bind(AvailabilityRepositoryInterface::class, EloquentAvailabilityRepository::class);
        $this->app->bind(MatchRepositoryInterface::class, EloquentMatchRepository::class);
        $this->app->bind(MessageRepositoryInterface::class, EloquentMessageRepository::class);
        $this->app->bind(RestaurantRepositoryInterface::class, EloquentRestaurantRepository::class);
        
        // Command Handlers
        $this->app->bind(CreateProfileCommandHandler::class, function ($app) {
            return new CreateProfileCommandHandler(
                $app->make(ProfileRepositoryInterface::class)
            );
        });
        
        $this->app->bind(UpdateProfileCommandHandler::class, function ($app) {
            return new UpdateProfileCommandHandler(
                $app->make(ProfileRepositoryInterface::class)
            );
        });
        
        $this->app->bind(CreateAvailabilityCommandHandler::class, function ($app) {
            return new CreateAvailabilityCommandHandler(
                $app->make(AvailabilityRepositoryInterface::class)
            );
        });
        
        $this->app->bind(CreateMatchCommandHandler::class, function ($app) {
            return new CreateMatchCommandHandler(
                $app->make(MatchRepositoryInterface::class)
            );
        });
        
        $this->app->bind(SendMessageCommandHandler::class, function ($app) {
            return new SendMessageCommandHandler(
                $app->make(MessageRepositoryInterface::class),
                $app->make(MatchRepositoryInterface::class)
            );
        });
        
        $this->app->bind(MarkMessageAsReadCommandHandler::class, function ($app) {
            return new MarkMessageAsReadCommandHandler(
                $app->make(MessageRepositoryInterface::class)
            );
        });
        
        // Query Handlers
        $this->app->bind(FindRestaurantsByLocationQueryHandler::class, function ($app) {
            return new FindRestaurantsByLocationQueryHandler(
                $app->make(RestaurantRepositoryInterface::class)
            );
        });
        
        $this->app->bind(GetConversationQueryHandler::class, function ($app) {
            return new GetConversationQueryHandler(
                $app->make(MessageRepositoryInterface::class),
                $app->make(MatchRepositoryInterface::class)
            );
        });
        
        $this->app->bind(GetProfilesByPreferencesQueryHandler::class, function ($app) {
            return new GetProfilesByPreferencesQueryHandler(
                $app->make(ProfileRepositoryInterface::class)
            );
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
