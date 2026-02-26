# Système de Gestion des Colocations - EasyColoc

## ✅ Fonctionnalités Implémentées

### 1. Création de Colocation
- ✅ Formulaire de création (nom, description)
- ✅ Owner automatique (créateur)
- ✅ Ajout automatique du créateur comme membre
- ✅ Status "active" par défaut
- ✅ **Blocage multi-colocation active** : Un utilisateur ne peut avoir qu'une seule colocation active

### 2. Affichage de Colocation
- ✅ Détails de la colocation (nom, description, status)
- ✅ Liste des membres actifs avec réputation
- ✅ Identification du propriétaire (badge "Owner")
- ✅ Badge de status (active/cancelled)
- ✅ Protection par policy (seuls owner et members peuvent voir)

### 3. Annulation de Colocation
- ✅ Bouton "Annuler la colocation" (owner uniquement)
- ✅ Confirmation avant annulation
- ✅ Changement du status à "cancelled"
- ✅ Message de confirmation
- ✅ Redirection vers dashboard

### 4. Liste des Colocations
- ✅ Affichage de toutes les colocations de l'utilisateur
- ✅ Bouton "Créer une colocation" (si aucune active)
- ✅ Message si colocation active existante
- ✅ Liens vers les détails de chaque colocation

## 📊 Structure Base de Données

### Table: colocations
```sql
- id
- name (varchar)
- description (text, nullable)
- owner_id (FK users)
- status (varchar, default: 'active')
- created_at
- updated_at
```

### Table: colocation_user (pivot)
```sql
- id
- colocation_id (FK colocations)
- user_id (FK users)
- left_at (timestamp, nullable)
- created_at
- updated_at
- UNIQUE(colocation_id, user_id)
```

## 🔧 Modèles

### Colocation
**Relations:**
- `owner()` - BelongsTo User
- `members()` - BelongsToMany User
- `activeMembers()` - Members avec left_at = null

**Méthodes:**
- `isOwner(User $user)` - Vérifie si propriétaire
- `isMember(User $user)` - Vérifie si membre
- `isActive()` - Vérifie si status = active

**Scopes:**
- `active()` - Filtre les colocations actives

### User
**Relations:**
- `colocations()` - BelongsToMany Colocation
- `ownedColocations()` - HasMany Colocation

**Méthodes:**
- `hasActiveColocation()` - Vérifie si l'utilisateur a une colocation active

## 🛡️ Policies

### ColocationPolicy
| Action | Admin | Owner | Member | User |
|--------|-------|-------|--------|------|
| view | ✅ | ✅ | ✅ | ❌ |
| create | ✅ | ✅ | ✅ | ✅ |
| update | ✅ | ✅ | ❌ | ❌ |
| delete | ✅ | ✅ | ❌ | ❌ |

## 🚀 Routes

```php
GET  /colocations/create          - Formulaire création
POST /colocations                 - Créer colocation (via Livewire)
GET  /colocations/{colocation}    - Afficher colocation
```

## 📝 Composants Livewire

### colocations.create
- Formulaire de création
- Validation multi-colocation active
- Redirection vers show après création

### colocations.show
- Affichage détails colocation
- Liste membres actifs
- Bouton annulation (owner uniquement)

### colocations.index
- Liste des colocations de l'utilisateur
- Bouton création (si pas de colocation active)
- Liens vers détails

## 🔒 Règles de Gestion

### Blocage Multi-Colocation Active
Un utilisateur ne peut avoir qu'une seule colocation active à la fois :
- ✅ Vérification lors de la création
- ✅ Message d'erreur si colocation active existante
- ✅ Bouton "Créer" masqué si colocation active
- ✅ Vérification via `User::hasActiveColocation()`

### Annulation
- Seul le owner peut annuler
- Status passe de "active" à "cancelled"
- Les membres restent attachés (historique)
- Permet à l'owner de créer une nouvelle colocation

## 🧪 Tests Manuels

### Test 1: Création de Colocation
1. Se connecter
2. Aller sur `/dashboard`
3. Cliquer sur "Créer une colocation"
4. Remplir nom et description
5. Soumettre
6. ✅ Vérifier redirection vers détails
7. ✅ Vérifier que l'utilisateur est owner et membre

### Test 2: Blocage Multi-Colocation
1. Créer une première colocation
2. Retourner au dashboard
3. ✅ Vérifier que le bouton "Créer" est masqué
4. ✅ Vérifier le message "Vous avez déjà une colocation active"
5. Essayer d'accéder à `/colocations/create` directement
6. Soumettre le formulaire
7. ✅ Vérifier le message d'erreur

### Test 3: Affichage Colocation
1. Accéder à une colocation
2. ✅ Vérifier affichage nom, description
3. ✅ Vérifier liste des membres
4. ✅ Vérifier badge "Owner" sur le propriétaire
5. ✅ Vérifier réputation des membres
6. ✅ Vérifier badge status "Active"

### Test 4: Annulation Colocation
1. En tant qu'owner, accéder à la colocation
2. Cliquer sur "Annuler la colocation"
3. Confirmer
4. ✅ Vérifier message de succès
5. ✅ Vérifier redirection vers dashboard
6. ✅ Vérifier status "Cancelled" dans la liste
7. ✅ Vérifier que le bouton "Créer" est à nouveau disponible

### Test 5: Protection Member
1. En tant que member (non-owner)
2. Accéder à la colocation
3. ✅ Vérifier que le bouton "Annuler" n'est pas visible

## 📋 Prochaines Étapes

- [ ] Système d'invitation par token
- [ ] Gestion des dépenses
- [ ] Calcul des balances
- [ ] Système de paiements
- [ ] Départ d'un membre
- [ ] Retrait d'un membre par l'owner
- [ ] Système de réputation
- [ ] Dashboard admin global

## 💡 Utilisation

```php
// Vérifier si un utilisateur a une colocation active
if (Auth::user()->hasActiveColocation()) {
    // Bloquer action
}

// Créer une colocation
$colocation = Colocation::create([
    'name' => 'Ma Coloc',
    'description' => 'Description',
    'owner_id' => Auth::id(),
    'status' => 'active',
]);
$colocation->members()->attach(Auth::id());

// Annuler une colocation
$colocation->update(['status' => 'cancelled']);

// Récupérer les membres actifs
$activeMembers = $colocation->activeMembers;

// Vérifier si owner
if ($colocation->isOwner($user)) {
    // Action owner
}
```
