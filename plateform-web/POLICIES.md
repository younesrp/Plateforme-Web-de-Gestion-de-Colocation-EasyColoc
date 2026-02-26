# Système de Permissions - EasyColoc

## ✅ Implémentation Complète

### Champs Utilisateur
- ✅ `role` (admin/user) - default: 'user'
- ✅ `is_banned` (boolean) - default: false
- ✅ `reputation` (integer) - default: 0

### Promotion Automatique
- ✅ Le premier utilisateur inscrit devient automatiquement **admin global**
- ✅ Implémenté via `UserObserver`

### Middleware Anti-Ban
- ✅ `CheckBanned` - Vérifie si l'utilisateur est banni
- ✅ Appliqué sur les routes protégées (dashboard, profile)
- ✅ Déconnexion automatique des utilisateurs bannis

## Policies Implémentées

### 1. BasePolicy
Politique de base héritée par toutes les autres policies :
- **Admin** : Accès total à toutes les actions
- **Banned** : Aucun accès

### 2. UserPolicy
Gestion des permissions utilisateurs :

| Action | Admin | Owner | User |
|--------|-------|-------|------|
| viewAny | ✅ | ✅ | ✅ |
| view | ✅ | ✅ | ✅ |
| create | ✅ | ✅ | ✅ |
| update | ✅ | ✅ (soi-même) | ❌ |
| delete | ✅ | ✅ (soi-même) | ❌ |
| ban | ✅ | ❌ | ❌ |
| updateRole | ✅ | ❌ | ❌ |

### 3. ColocationPolicy
Gestion des permissions colocations :

| Action | Admin | Owner | Member | User |
|--------|-------|-------|--------|------|
| viewAny | ✅ | ✅ | ✅ | ✅ |
| view | ✅ | ✅ | ✅ | ❌ |
| create | ✅ | ✅ | ✅ | ✅ |
| update | ✅ | ✅ | ❌ | ❌ |
| delete | ✅ | ✅ | ❌ | ❌ |
| addMember | ✅ | ✅ | ❌ | ❌ |
| removeMember | ✅ | ✅ | ❌ | ❌ |
| leave | ✅ | ❌ | ✅ | ❌ |

## Utilisation dans les Contrôleurs

```php
// Vérifier une permission
$this->authorize('update', $colocation);

// Vérifier dans une condition
if ($user->can('update', $colocation)) {
    // Action autorisée
}

// Vérifier dans les routes
Route::put('/colocations/{colocation}', function (Colocation $colocation) {
    Gate::authorize('update', $colocation);
    // ...
});
```

## Utilisation dans Blade

```blade
@can('update', $colocation)
    <button>Modifier</button>
@endcan

@cannot('delete', $colocation)
    <p>Vous ne pouvez pas supprimer cette colocation</p>
@endcannot

@canany(['update', 'delete'], $colocation)
    <div>Actions disponibles</div>
@endcanany
```

## Méthodes Helper (Models)

### User
```php
$user->isAdmin(); // Vérifie si admin
$user->isBanned(); // Vérifie si banni
```

### Colocation
```php
$colocation->isOwner($user); // Vérifie si propriétaire
$colocation->isMember($user); // Vérifie si membre
```

## Structure Base de Données

### Table: colocations
- id
- name
- description
- owner_id (FK users)
- timestamps

### Table: colocation_user (pivot)
- id
- colocation_id (FK colocations)
- user_id (FK users)
- timestamps
- unique(colocation_id, user_id)

## Migration

```bash
php artisan migrate
```

## Tests

```php
// Test promotion premier utilisateur
$user = User::factory()->create();
assertTrue($user->isAdmin());

// Test policy Owner
$colocation = Colocation::factory()->create(['owner_id' => $user->id]);
assertTrue($user->can('update', $colocation));

// Test policy Member
$member = User::factory()->create();
$colocation->members()->attach($member);
assertTrue($member->can('view', $colocation));
assertFalse($member->can('update', $colocation));

// Test policy Admin
$admin = User::factory()->create(['role' => 'admin']);
assertTrue($admin->can('update', $colocation));
assertTrue($admin->can('delete', $colocation));
```

## Hiérarchie des Permissions

1. **Admin Global** : Accès total
2. **Owner** : Contrôle total sur sa colocation
3. **Member** : Accès lecture + quitter la colocation
4. **User** : Créer des colocations, voir les siennes
5. **Banned** : Aucun accès
