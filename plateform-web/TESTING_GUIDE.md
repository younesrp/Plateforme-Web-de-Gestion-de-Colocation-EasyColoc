# Guide de Test - Authentification EasyColoc

## Installation et Configuration

### 1. Exécuter les migrations
```bash
php artisan migrate
```

### 2. Créer des utilisateurs de test (optionnel)
```bash
php artisan db:seed --class=AdminUserSeeder
```

Cela créera :
- **Admin** : admin@easycoloc.com / password
- **User** : user@easycoloc.com / password

## Tests Manuels

### Test 1: Inscription
1. Accéder à `/register`
2. Remplir le formulaire :
   - Nom : Test User
   - Email : test@example.com
   - Mot de passe : password123
   - Confirmation : password123
3. Cliquer sur "Register"
4. ✅ Vérifier la redirection vers `/dashboard`
5. ✅ Vérifier que l'utilisateur est connecté

### Test 2: Connexion
1. Se déconnecter
2. Accéder à `/login`
3. Entrer les identifiants :
   - Email : test@example.com
   - Mot de passe : password123
4. Cliquer sur "Log in"
5. ✅ Vérifier la redirection vers `/dashboard`

### Test 3: Déconnexion
1. Cliquer sur le menu utilisateur (en haut à droite)
2. Cliquer sur "Log Out"
3. ✅ Vérifier la redirection vers `/`
4. ✅ Vérifier que l'utilisateur est déconnecté

### Test 4: Gestion du Profil
1. Se connecter
2. Accéder à `/profile`
3. Modifier le nom : "Nouveau Nom"
4. Cliquer sur "Save"
5. ✅ Vérifier le message "Saved."
6. ✅ Vérifier que le nom est mis à jour dans le menu

### Test 5: Changement de Mot de Passe
1. Sur la page `/profile`
2. Scroller vers "Update Password"
3. Remplir :
   - Current Password : password123
   - New Password : newpassword123
   - Confirm Password : newpassword123
4. Cliquer sur "Save"
5. ✅ Vérifier le message de succès
6. Se déconnecter et se reconnecter avec le nouveau mot de passe

### Test 6: Protection Anti-Ban
1. Avec un outil comme phpMyAdmin ou Tinker :
   ```php
   $user = User::where('email', 'test@example.com')->first();
   $user->is_banned = true;
   $user->save();
   ```
2. Essayer de se connecter
3. ✅ Vérifier le message "Votre compte a été banni."
4. ✅ Vérifier que la connexion est refusée

### Test 7: Rate Limiting
1. Essayer de se connecter 6 fois avec un mauvais mot de passe
2. ✅ Vérifier le message de throttling après 5 tentatives

## Vérification en Base de Données

```sql
-- Vérifier les champs ajoutés
SELECT id, name, email, role, reputation, is_banned FROM users;
```

## Commandes Utiles

```bash
# Réinitialiser la base de données
php artisan migrate:fresh

# Réinitialiser avec les seeders
php artisan migrate:fresh --seed

# Créer un utilisateur via Tinker
php artisan tinker
>>> User::create(['name' => 'Test', 'email' => 'test@test.com', 'password' => Hash::make('password')])

# Bannir un utilisateur
>>> User::where('email', 'test@test.com')->update(['is_banned' => true])
```

## Endpoints API (si nécessaire plus tard)

Les routes actuelles utilisent Livewire/Volt, mais peuvent être converties en API :
- POST /api/register
- POST /api/login
- POST /api/logout
- GET /api/user
- PUT /api/user/profile
- PUT /api/user/password
