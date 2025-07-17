<?php

declare(strict_types=1);

namespace App\Providers;

use App\CQRS\CommandHandlers\CreateProfileCommandHandler;
use App\CQRS\QueryHandlers\GetProfileByUserIdQueryHandler;
use App\Repositories\Interfaces\AvailabilityRepositoryInterface;
use App\Repositories\Interfaces\MessageRepositoryInterface;
use App\Repositories\Interfaces\ProfileRepositoryInterface;
use App\Repositories\Interfaces\RatingRepositoryInterface;
use App\Repositories\Interfaces\RestaurantRepositoryInterface;
use App\Repositories\Interfaces\UserMatchRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Models\Availability;
use App\Models\Message;
use App\Models\Profile;
use App\Models\Rating;
use App\Models\Restaurant;
use App\Models\User;
use App\Models\UserMatch;
use App\Repositories\AvailabilityRepository;
use App\Repositories\MessageRepository;
use App\Repositories\ProfileRepository;
use App\Repositories\RatingRepository;
use App\Repositories\RestaurantRepository;
use App\Repositories\UserMatchRepository;
use App\Repositories\UserRepository;
// Services de disponibilité supprimés
use App\Services\MatchingService;
use App\Services\MessageService;
use App\Services\ProfileService;
use App\Services\RatingService;
use App\Services\RestaurantSearchService;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Repositories
        $this->app->bind(UserRepositoryInterface::class, function ($app) {
            return new UserRepository($app->make(User::class));
        });

        $this->app->bind(ProfileRepositoryInterface::class, function ($app) {
            return new ProfileRepository($app->make(Profile::class));
        });
        
        $this->app->bind(AvailabilityRepositoryInterface::class, function ($app) {
            return new AvailabilityRepository($app->make(Availability::class));
        });
        
        $this->app->bind(UserMatchRepositoryInterface::class, function ($app) {
            return new UserMatchRepository($app->make(UserMatch::class));
        });
        
        $this->app->bind(MessageRepositoryInterface::class, function ($app) {
            return new MessageRepository($app->make(Message::class));
        });
        
        $this->app->bind(RatingRepositoryInterface::class, function ($app) {
            return new RatingRepository($app->make(Rating::class));
        });
        
        $this->app->bind(RestaurantRepositoryInterface::class, function ($app) {
            return new RestaurantRepository($app->make(Restaurant::class));
        });

        // Command Handlers
        $this->app->bind(CreateProfileCommandHandler::class, function ($app) {
            return new CreateProfileCommandHandler(
                $app->make(ProfileRepositoryInterface::class)
            );
        });

        // Query Handlers
        $this->app->bind(GetProfileByUserIdQueryHandler::class, function ($app) {
            return new GetProfileByUserIdQueryHandler(
                $app->make(ProfileRepositoryInterface::class)
            );
        });

        // Services
        $this->app->bind(ProfileService::class, function ($app) {
            return new ProfileService(
                $app->make(CreateProfileCommandHandler::class),
                $app->make(GetProfileByUserIdQueryHandler::class)
            );
        });
        
        // Services de disponibilité supprimés
        
        $this->app->bind(MatchingService::class, function ($app) {
            return new MatchingService(
                $app->make(AvailabilityRepositoryInterface::class),
                $app->make(UserRepositoryInterface::class),
                $app->make(UserMatchRepositoryInterface::class)
            );
        });
        
        $this->app->bind(MessageService::class, function ($app) {
            return new MessageService(
                $app->make(MessageRepositoryInterface::class),
                $app->make(UserMatchRepositoryInterface::class)
            );
        });
        
        $this->app->bind(RatingService::class, function ($app) {
            return new RatingService(
                $app->make(RatingRepositoryInterface::class),
                $app->make(UserMatchRepositoryInterface::class)
            );
        });
        
        $this->app->bind(RestaurantSearchService::class, function ($app) {
            return new RestaurantSearchService(
                $app->make(RestaurantRepositoryInterface::class)
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
