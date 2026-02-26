# Système d'Authentification - EasyColoc

## Fonctionnalités Implémentées

### 1. Inscription
- Route: `/register`
- Champs: nom, email, mot de passe, confirmation mot de passe
- Validation automatique
- Création de compte avec rôle "user" par défaut
- Réputation initialisée à 0

### 2. Connexion
- Route: `/login`
- Champs: email, mot de passe, "Se souvenir de moi"
- Protection contre les tentatives multiples (rate limiting)
- Vérification anti-ban automatique
- Redirection vers le dashboard après connexion

### 3. Déconnexion
- Accessible via le menu utilisateur
- Invalidation de la session
- Redirection vers la page d'accueil

### 4. Gestion du Profil
- Route: `/profile`
- Mise à jour du nom et email
- Changement de mot de passe
- Suppression de compte
- Vérification email si modifié

## Nouveaux Champs Utilisateur

- `role`: string (default: 'user') - Rôle de l'utilisateur (user/admin)
- `reputation`: integer (default: 0) - Score de réputation
- `is_banned`: boolean (default: false) - Statut de bannissement

## Middleware

### CheckBanned
- Vérifie si l'utilisateur est banni
- Déconnecte automatiquement les utilisateurs bannis
- Appliqué sur les routes: dashboard, profile

## Méthodes Helper (User Model)

- `isAdmin()`: Vérifie si l'utilisateur est admin
- `isBanned()`: Vérifie si l'utilisateur est banni

## Migration à Exécuter

```bash
php artisan migrate
```

Cette commande créera les nouveaux champs dans la table users.

## Routes Disponibles

- `GET /register` - Formulaire d'inscription
- `POST /register` - Traitement de l'inscription
- `GET /login` - Formulaire de connexion
- `POST /login` - Traitement de la connexion
- `POST /logout` - Déconnexion
- `GET /profile` - Page de profil
- `PUT /profile` - Mise à jour du profil
- `PUT /password` - Changement de mot de passe
- `DELETE /profile` - Suppression de compte

## Sécurité

- Mots de passe hashés avec bcrypt
- Protection CSRF sur tous les formulaires
- Rate limiting sur la connexion (5 tentatives max)
- Validation des emails
- Vérification anti-ban
- Sessions sécurisées
