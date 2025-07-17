<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\DTO\ProfileDTO;
use App\Models\Profile;
use App\Repositories\Interfaces\ProfileRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

final class EloquentProfileRepository extends AbstractEloquentRepository implements ProfileRepositoryInterface
{
    /**
     * @param Profile $profile
     */
    public function __construct(
        private Profile $profile
    ) {
        $this->model = $profile;
    }

    /**
     * Crée un nouveau profil
     *
     * @param ProfileDTO $profileDTO
     * @return ProfileDTO
     */
    public function createFromDTO(ProfileDTO $profileDTO): ProfileDTO
    {
        // Convertir le DTO en tableau et extraire les données nécessaires
        $data = $profileDTO->toArray();
        
        // Créer le profil avec les données disponibles
        $profile = $this->profile->create([
            'user_id' => auth()->id(), // Utiliser l'utilisateur authentifié ou un autre moyen de récupérer l'ID utilisateur
            'bio' => $profileDTO->getBio(),
            'preferences' => json_encode($profileDTO->getPreferences()),
            'dietary_restrictions' => json_encode($profileDTO->getDietaryRestrictions()),
            'favorite_cuisines' => json_encode($profileDTO->getFavoriteCuisines()),
            'avatar_url' => $profileDTO->getAvatarUrl(),
        ]);

        return ProfileDTO::fromArray($profile->toArray());
    }

    /**
     * Met à jour un profil existant
     *
     * @param int $id
     * @param ProfileDTO $profileDTO
     * @return ProfileDTO
     */
    public function update(int $id, ProfileDTO $profileDTO): ProfileDTO
    {
        $profile = $this->profile->findOrFail($id);
        
        $profile->update([
            'first_name' => $profileDTO->getFirstName(),
            'last_name' => $profileDTO->getLastName(),
            'bio' => $profileDTO->getBio(),
            'birth_date' => $profileDTO->getBirthDate(),
            'gender' => $profileDTO->getGender(),
            'preferences' => json_encode($profileDTO->getPreferences()),
            'avatar' => $profileDTO->getAvatar(),
        ]);

        return ProfileDTO::fromArray($profile->toArray());
    }

    /**
     * Trouve un profil par son ID
     *
     * @param int $id
     * @return ProfileDTO|null
     */
    public function findById(int $id): ?ProfileDTO
    {
        $profile = $this->profile->find($id);
        
        if (!$profile) {
            return null;
        }
        
        return ProfileDTO::fromArray($profile->toArray());
    }

    /**
     * Trouve des profils par préférences
     *
     * @param array $preferences
     * @param int $page
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function findByPreferences(array $preferences, int $page = 1, int $perPage = 10): LengthAwarePaginator
    {
        $query = $this->profile->query();
        
        // Filtrer par préférences
        foreach ($preferences as $key => $value) {
            // Utiliser JSON_CONTAINS pour rechercher dans le champ JSON preferences
            $query->whereRaw("JSON_CONTAINS(preferences, ?, '$.{$key}')", [json_encode($value)]);
        }
        
        $paginator = $query->paginate($perPage, ['*'], 'page', $page);
        
        // Transformer les résultats en DTOs
        $paginator->setCollection(
            $paginator->getCollection()->map(function ($profile) {
                return ProfileDTO::fromArray($profile->toArray());
            })
        );
        
        return $paginator;
    }
    
    /**
     * Find profile by user ID
     * 
     * @param int $userId
     * @return \App\Models\Profile|null
     */
    public function findByUserId(int $userId)
    {
        return $this->profile->where('user_id', $userId)->first();
    }
}
