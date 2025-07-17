<?php

namespace App\Http\Controllers\Availability;

use App\Http\Controllers\Controller;
use App\Models\Availability;
use App\Repositories\Interfaces\AvailabilityRepositoryInterface;
use App\Services\GeolocationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ManageAvailabilityController extends Controller
{
    public function __construct(
        private AvailabilityRepositoryInterface $availabilityRepository,
        private GeolocationService $geolocationService
    ) {}

    /**
     * Affiche la page de gestion des disponibilités
     */
    public function index()
    {
        $user = Auth::user();
        
        // Récupérer les disponibilités de l'utilisateur
        $availabilities = $user->availabilities;
        
        // Préparer les données pour la vue
        $availabilityData = [];
        foreach ($availabilities as $availability) {
            $availabilityData[] = [
                'id' => $availability->id,
                'is_active' => $availability->is_active,
                'activity' => $availability->preferences['activity'] ?? 'eat',
                'latitude' => $availability->latitude,
                'longitude' => $availability->longitude,
            ];
        }
        
        return Inertia::render('Availability/Manage', [
            'availabilities' => $availabilityData,
            'activities' => [
                ['value' => 'eat', 'label' => 'Manger'],
                ['value' => 'drink', 'label' => 'Boire'],
                ['value' => 'chat', 'label' => 'Discuter'],
            ],
            'userProfile' => [
                'latitude' => $user->profile->latitude ?? null,
                'longitude' => $user->profile->longitude ?? null,
            ]
        ]);
    }

    /**
     * Met à jour ou crée une disponibilité
     */
    public function update(Request $request)
    {
        $request->validate([
            'is_active' => 'required|boolean',
            'activity' => 'required|string|in:eat,drink,chat',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);
        
        $user = Auth::user();
        
        // Chercher une disponibilité existante pour cette activité
        $availability = $user->availabilities()
            ->whereJsonContains('preferences->activity', $request->activity)
            ->first();
        
        // Si aucune disponibilité n'existe pour cette activité, en créer une nouvelle
        if (!$availability) {
            $availability = new Availability([
                'user_id' => $user->id,
                'is_active' => $request->is_active,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'preferences' => [
                    'activity' => $request->activity,
                    'gender' => $user->preferences['gender'] ?? 'any',
                ],
            ]);
        } else {
            // Mettre à jour la disponibilité existante
            $availability->is_active = $request->is_active;
            $availability->latitude = $request->latitude;
            $availability->longitude = $request->longitude;
            
            // Mettre à jour les préférences tout en conservant les autres valeurs
            $preferences = $availability->preferences;
            $preferences['activity'] = $request->activity;
            $availability->preferences = $preferences;
        }
        
        $availability->save();
        
        return redirect()->back()->with('success', 'Disponibilité mise à jour avec succès');
    }

    /**
     * Désactive toutes les disponibilités de l'utilisateur
     */
    public function disableAll()
    {
        $user = Auth::user();
        
        $user->availabilities()->update(['is_active' => false]);
        
        return redirect()->back()->with('success', 'Toutes les disponibilités ont été désactivées');
    }
}
