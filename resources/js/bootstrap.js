import axios from 'axios';
window.axios = axios;

// Configuration d'Axios pour les requêtes API
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.headers.common['Accept'] = 'application/json';
window.axios.defaults.headers.common['Content-Type'] = 'application/json';

// Configuration pour l'authentification web standard
window.axios.defaults.withCredentials = true;

// Définir l'URL de base pour les requêtes API
window.axios.defaults.baseURL = window.location.origin;

// Ajouter le token CSRF à toutes les requêtes
const token = document.head.querySelector('meta[name="csrf-token"]');
if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}
