<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Profile;
use App\Models\Availability;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Désactiver les contraintes de clés étrangères pour éviter les erreurs
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        
        // Nettoyer les tables existantes
        User::truncate();
        Profile::truncate();
        Availability::truncate();
        
        // Réactiver les contraintes de clés étrangères
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
        
        // Créer des utilisateurs avec leurs profils et disponibilités
        User::factory()
            ->count(20)
            ->has(
                Profile::factory()
                    ->state(function (array $attributes, User $user) {
                        return [
                            'user_id' => $user->id,
                        ];
                    })
            )
            ->create()
            ->each(function (User $user) {
                // Pour chaque utilisateur, créer 1 à 3 disponibilités
                $activitiesCount = rand(1, 3);
                $activities = ['eat', 'drink', 'chat'];
                $selectedActivities = array_rand(array_flip($activities), $activitiesCount);
                
                if (!is_array($selectedActivities)) {
                    $selectedActivities = [$selectedActivities];
                }
                
                foreach ($selectedActivities as $activity) {
                    Availability::factory()
                        ->forActivity($activity)
                        ->active() // Toutes les disponibilités sont actives pour faciliter les tests
                        ->create([
                            'user_id' => $user->id,
                            'latitude' => $user->profile->latitude,
                            'longitude' => $user->profile->longitude,
                        ]);
                }
            });
            
        // Créer un utilisateur de test avec un mot de passe connu
        $testUser = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'gender' => 'male',
        ]);
        
        // Créer un profil pour l'utilisateur de test
        Profile::factory()->create([
            'user_id' => $testUser->id,
            'bio' => 'Ceci est un utilisateur de test pour faciliter les démonstrations.',
            'latitude' => 48.8566, // Paris
            'longitude' => 2.3522,
        ]);
        
        // Créer des disponibilités pour l'utilisateur de test
        foreach (['eat', 'drink', 'chat'] as $activity) {
            Availability::factory()
                ->forActivity($activity)
                ->active()
                ->create([
                    'user_id' => $testUser->id,
                    'latitude' => 48.8566, // Paris
                    'longitude' => 2.3522,
                ]);
        }
        
        $this->command->info('Base de données peuplée avec succès ! 20 utilisateurs créés avec leurs profils et disponibilités.');
        $this->command->info('Utilisateur de test créé : test@example.com / password');
    }
}
