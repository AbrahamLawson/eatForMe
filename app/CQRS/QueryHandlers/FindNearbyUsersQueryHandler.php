<?php

declare(strict_types=1);

namespace App\CQRS\QueryHandlers;

use App\CQRS\Queries\FindNearbyUsersQuery;
use App\CQRS\Queries\QueryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Services\GeolocationService;
use App\Services\MatchingService;
use InvalidArgumentException;

final readonly class FindNearbyUsersQueryHandler implements QueryHandlerInterface
{
    /**
     * FindNearbyUsersQueryHandler constructor.
     *
     * @param UserRepositoryInterface $userRepository
     * @param GeolocationService $geolocationService
     * @param MatchingService $matchingService
     */
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private GeolocationService $geolocationService,
        private MatchingService $matchingService
    ) {
    }

    /**
     * Handle the query.
     *
     * @param QueryInterface $query
     * @return array
     * @throws InvalidArgumentException
     */
    public function handle(QueryInterface $query): array
    {
        if (!$query instanceof FindNearbyUsersQuery) {
            throw new InvalidArgumentException('Query must be an instance of FindNearbyUsersQuery');
        }

        // Récupérer l'utilisateur actuel avec ses préférences et disponibilités actives
        $currentUser = $this->userRepository->findUserWithPreferencesAndAvailabilities($query->getUserId());
        
        // Trouver les utilisateurs à proximité
        $nearbyUsers = $this->geolocationService->findNearbyUsers(
            $currentUser,
            $query->getLatitude(),
            $query->getLongitude(),
            $query->getDistance(),
            $query->getActivity(),
            $query->getProfile()
        );
        
        // Si aucun utilisateur n'est trouvé, retourner un tableau vide
        if (empty($nearbyUsers)) {
            return [
                'users' => [],
                'potential_matches' => []
            ];
        }
        
        // Identifier les meilleurs matchs potentiels
        $potentialMatches = $this->matchingService->findPotentialMatches(
            $currentUser,
            $nearbyUsers,
            $query->getActivity(),
            $query->getProfile() // Ajout du paramètre profile
        );
        
        return [
            'users' => $nearbyUsers,
            'potential_matches' => $potentialMatches
        ];
    }
}
