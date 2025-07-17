# Configuration du Projet EatMe

Ce fichier contient toutes les règles et configurations à respecter pour ce projet.

## Concept de l'Application
EatMe est une application qui permet aux utilisateurs de se rencontrer autour d'un repas au restaurant :
- Les utilisateurs indiquent leur disponibilité pour aller manger
- D'autres utilisateurs peuvent voir ces disponibilités sur une carte ou via un système de swipe
- Les utilisateurs peuvent faire des propositions/demandes pour manger ensemble
- Chaque demande affiche des critères (qui paie, type de restaurant préféré, etc.)

## Règles Générales
- Je suis Intermédiaire en Laravel et débutant en Vue.js, je veux que à chaque fois que tu vas coder ou me donner une solution, tu m'expliques pourquoi on fait ça et qu'on code avec les bonnes pratiques et les standards recommandés.

## Structure du Projet
- Backend : Laravel (API RESTful)
- Frontend : Vue.js
- Base de données : MySQL
- Structure MVC classique pour Laravel
- Architecture par composants pour Vue.js

## Standards de Code
- **Architecture** :
  - Pattern Repository pour la séparation des préoccupations
  - Interface + Implémentation pour chaque repository
  - Injection de dépendances pour les repositories
  - **Contrôleurs** :
    - Principe de responsabilité unique (SRP) : chaque contrôleur doit avoir une seule responsabilité bien définie
    - Utilisation de contrôleurs invocables (`__invoke()`) qui ne font qu'une seule chose
    - Un contrôleur = une action (ex: CreateProfileController, UpdateProfileController, etc.)
    - Les contrôleurs doivent déléguer la logique métier aux services CQRS
  - **Services** :
    - Principe de responsabilité unique (SRP) : chaque service doit avoir une seule responsabilité bien définie
    - Les services orchestrent les opérations spécifiques du domaine métier
    - Chaque service doit être spécialisé dans une tâche précise plutôt que de gérer plusieurs aspects
    - Exemples : MatchingService (uniquement pour le matching), NotificationService (uniquement pour les notifications)
  - Pattern CQRS (Command Query Responsibility Segregation):
    - Commands pour les opérations d'écriture (create, update, delete)
    - Queries pour les opérations de lecture
    - CommandHandlers et QueryHandlers pour traiter les requêtes
    - DTOs (Data Transfer Objects) pour les données entrantes et sortantes
  - **Requêtes SQL** :
    - Utilisation de requêtes SQL brutes via DB::select() pour les opérations complexes
    - Hydratation des modèles Eloquent avec les résultats des requêtes brutes
    - Utilisation de requêtes paramétrées pour éviter les injections SQL
    - Formules géospatiales (comme Haversine) pour les calculs de distance
- **Conventions de nommage** :
  - Controllers : PascalCase, suffixe Controller (ex: UserController)
  - Models : PascalCase, singulier (ex: User)
  - Tables : snake_case, pluriel (ex: users)
  - Migrations : snake_case avec date (ex: 2023_06_09_create_users_table)
  - Components Vue : PascalCase (ex: UserProfile)
  - Repositories : PascalCase, suffixe Repository (ex: UserRepository)
  - Interfaces : PascalCase, préfixe I ou suffixe Interface (ex: IUserRepository ou UserRepositoryInterface)
- **Style de code** : PSR-12 pour PHP, Style Guide officiel pour Vue.js
- **Indentation** : 4 espaces pour PHP, 2 espaces pour JS/Vue
- **Commentaires** : DocBlocks pour les classes et méthodes importantes
- **Typage strict** : Toutes les classes PHP doivent inclure `declare(strict_types=1);` pour garantir un typage strict
- **Classes immuables** : Les classes CQRS (Commands, Queries, CommandHandlers, QueryHandlers) doivent être déclarées comme `final readonly` lorsqu'elles ne sont pas destinées à être étendues, pour garantir l'immuabilité et optimiser les performances

## Technologies Utilisées
- Laravel 10.x
- Vue.js 3.x
- MySQL 8.0
- PHP 8.1+
- Node.js 16+
- Composer
- npm
- Laravel Sanctum pour l'authentification API
- Laravel Socialite pour l'authentification sociale (optionnel)
- Tailwind CSS pour le styling
- Axios pour les requêtes HTTP
- Leaflet ou Google Maps API pour la carte

## Fonctionnalités Principales
- Authentification des utilisateurs
- Profils utilisateurs avec préférences culinaires
- Système de géolocalisation
- Système de disponibilité (statut actif/inactif)
- Système de matching/swipe
- Messagerie entre utilisateurs
- Système de notation après les rencontres
- Filtres de recherche (distance, type de cuisine, qui paie, etc.)

## Processus de Développement
- **Workflow Git** : Feature branches avec pull requests
- **Processus de revue de code** : Code review avant merge
- **Tests** : PHPUnit pour le backend, Jest pour le frontend

## Déploiement
- **Environnements** : Développement, Staging, Production
- **CI/CD** : GitHub Actions ou GitLab CI

## Autres Règles Spécifiques
- Sécurité : Protection contre les injections SQL, XSS, CSRF
- Performance : Optimisation des requêtes, lazy loading des images
- UX : Interface intuitive, feedback utilisateur, temps de chargement minimisé
- **Gestion des fichiers** : TOUJOURS vérifier l'existence des fichiers et dossiers dans l'ensemble du projet avant d'en créer de nouveaux pour éviter les duplications et respecter l'architecture existante
- **Contrôleurs** : Les contrôleurs métier dans ce projet sont des classes autonomes (standalone) qui n'étendent PAS la classe `Controller` de Laravel. Ils doivent être définis comme `final readonly class` pour garantir l'immutabilité et respecter l'architecture CQRS stricte. Exception : les contrôleurs d'authentification (dans le dossier `Auth`) générés par Laravel peuvent conserver leur structure d'origine.

## Architecture Frontend Vue.js

### Modularité des Composants
- Privilégier la création de petits composants réutilisables plutôt que de gros fichiers monolithiques
- Extraire les éléments répétitifs ou complexes en composants autonomes
- Exemples de composants réutilisables :
  - `MediaCarousel.vue` : Pour l'affichage de médias (photos/vidéos) avec navigation
  - `InterestBadges.vue` : Pour l'affichage des centres d'intérêt utilisateur
  - `CuisinePreference.vue` : Pour l'affichage des préférences culinaires

### Avantages de cette approche
- Meilleure maintenabilité : Les composants plus petits sont plus faciles à comprendre et à maintenir
- Réutilisabilité : Ces composants peuvent être utilisés dans d'autres parties de l'application
- Cohérence : L'affichage des éléments récurrents sera cohérent partout dans l'application
- Tests simplifiés : Il est plus facile de tester des composants isolés
- Séparation des responsabilités : Chaque composant a une responsabilité unique et claire

---

## Directives de Design

### 🎨 Palette de Couleurs – Branding "Rencontres au resto"

| Couleur | Usage principal | Code HEX | Signification |
|---------|----------------|----------|--------------||
| 🍓 Rouge framboise | Call-to-action, boutons, logo accent | #EF476F | Appétit, passion, chaleur |
| 🍊 Orange doux | Hover, highlights | #FFA552 | Énergie, spontanéité, fun |
| 🌿 Vert menthe clair | Disponibilité, statut en ligne | #3ECF8E | Fraîcheur, confiance, safe |
| ☁️ Blanc cassé | Fond général | #FDFDFD | Neutralité, lisibilité, modernité |
| 🖤 Gris charbon doux | Texte principal | #333333 | Lisibilité, sérieux, contraste |
| 🫧 Gris clair | Bordures, séparateurs | #E8E8E8 | Discret, épuré, clair |

### 🔤 Typographie recommandée

- **Titre** : Poppins Bold → Moderne et amical
- **Texte** : Inter Regular → Lisible, clean, mobile-friendly

### 💡 Conseils UI

- Utilise un badge vert menthe pour dire "Dispo pour un resto"
- Les propositions reçues pourraient être affichées comme des cartes swipeables à la Tinder, avec un petit encart "Invite ou chacun paie ?" et le type de resto préféré (🍣, 🍔, 🥗...)
- En mode carte, les avatars apparaissent avec un halo coloré vert quand les gens sont disponibles maintenant

### 🧠 Résumé branding

- **Émotion** : appétit, fun, ouverture, connexion humaine
- **Sensation visuelle** : douce, moderne, claire, accueillante
- **Couleurs** : rouge framboise (fun), orange (énergie), vert menthe (sécurité), blanc cassé (pureté)

*Note: Veuillez remplir les sections ci-dessus avec les règles spécifiques que vous souhaitez appliquer à ce projet.*
*Note: Veuillez remplir les sections ci-dessus avec les règles spécifiques que vous souhaitez appliquer à ce projet.*
