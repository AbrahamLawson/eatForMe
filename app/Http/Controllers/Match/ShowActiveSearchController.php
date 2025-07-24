<?php

declare(strict_types=1);

namespace App\Http\Controllers\Match;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

final class ShowActiveSearchController
{
    /**
     * Affiche la page de recherche active.
     *
     * @return Response
     */
    public function __invoke(): Response
    {
        /** @var User $user */
        $user = Auth::user();
        
        // Récupérer les préférences de l'utilisateur depuis l'attribut preferences
        $userPreferences = $user->preferences ?? [];
        
        // S'assurer que les préférences sont un array (pas une string JSON)
        if (is_string($userPreferences)) {
            $userPreferences = json_decode($userPreferences, true) ?? [];
        }
        
        return Inertia::render('ActiveSearch/Index', [
            'googleMapsApiKey' => config('services.google.maps.api_key'),
            'userPreferences' => $userPreferences,
        ]);
    }
}
