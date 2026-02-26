# Système de Memberships - EasyColoc

## ✅ Tâches Accomplies

### 1. ✅ Créer table memberships (pivot)
**Table:** `colocation_user`

**Structure:**
```sql
- id
- colocation_id (FK colocations)
- user_id (FK users)
- left_at (timestamp, nullable)
- created_at
- updated_at
- UNIQUE(colocation_id, user_id)
```

**Status:** ✅ Table créée et migrée

---

### 2. ✅ Ajouter left_at
**Migration:** `2026_02_24_122200_add_left_at_to_colocation_user_table.php`

**Champ:** `left_at` (timestamp, nullable)

**Utilisation:**
- `null` = membre actif
- `timestamp` = membre parti (date de départ)

**Relations mises à jour:**
- `User::colocations()` - withPivot('left_at')
- `Colocation::members()` - withPivot('left_at')
- `Colocation::activeMembers()` - wherePivot('left_at', null)

**Status:** ✅ Champ ajouté et relations configurées

---

### 3. ✅ Implémenter départ membre
**Composant:** `resources/views/livewire/colocations/leave-button.blade.php`

**Fonctionnalités:**
- Bouton "Quitter la colocation"
- Confirmation avant action
- Mise à jour du pivot avec `left_at = now()`
- Message de succès
- Redirection vers dashboard
- Protection par policy `leave`

**Logique:**
```php
$colocation->members()->updateExistingPivot(Auth::id(), [
    'left_at' => now(),
]);
```

**Status:** ✅ Départ fonctionnel

---

### 4. ✅ Empêcher owner de quitter
**Règles implémentées:**

1. **Dans le composant leave-button:**
   - Vérification `!$colocation->isOwner(Auth::user())`
   - Message d'erreur si owner tente de quitter
   - Bouton masqué pour l'owner

2. **Dans la policy:**
   - `ColocationPolicy::leave()` retourne false pour owner

3. **Logique métier:**
   - L'owner doit annuler la colocation (status = 'cancelled')
   - L'owner ne peut pas simplement partir

**Status:** ✅ Owner bloqué, ne peut pas quitter

---

## 📊 Flux de Départ

### Membre Normal
1. Accède à la colocation
2. Voit le bouton "Quitter la colocation"
3. Clique et confirme
4. `left_at` = now()
5. N'apparaît plus dans les membres actifs
6. Peut créer/rejoindre une autre colocation

### Owner
1. Accède à la colocation
2. Ne voit PAS le bouton "Quitter"
3. Doit utiliser "Annuler la colocation"
4. Annulation change le status à 'cancelled'
5. Tous les membres restent attachés (historique)

---

## 🔧 Méthodes Utiles

### Colocation
```php
// Récupérer membres actifs uniquement
$colocation->activeMembers; // left_at = null

// Récupérer tous les membres (actifs + partis)
$colocation->members;

// Vérifier si un user est membre actif
$colocation->activeMembers()->where('user_id', $userId)->exists();
```

### User
```php
// Vérifier si a une colocation active
$user->hasActiveColocation(); // status = active ET left_at = null

// Récupérer colocations actives
$user->colocations()
    ->where('status', 'active')
    ->wherePivot('left_at', null)
    ->get();
```

---

## 🛡️ Policy

### ColocationPolicy::leave()
```php
public function leave(User $user, Colocation $colocation): bool
{
    return $colocation->isMember($user) && !$colocation->isOwner($user);
}
```

**Règles:**
- ✅ Doit être membre
- ✅ Ne doit PAS être owner
- ✅ Admin peut quitter (via before())

---

## 🧪 Tests

### Test 1: Départ Membre Normal
1. Se connecter en tant que member (non-owner)
2. Accéder à la colocation
3. Cliquer "Quitter la colocation"
4. Confirmer
5. ✅ Vérifier message "Vous avez quitté la colocation"
6. ✅ Vérifier redirection vers dashboard
7. ✅ Vérifier left_at rempli en BDD
8. ✅ Vérifier absence dans activeMembers
9. ✅ Vérifier bouton "Créer" réapparaît

### Test 2: Owner Bloqué
1. Se connecter en tant qu'owner
2. Accéder à la colocation
3. ✅ Vérifier bouton "Quitter" absent
4. ✅ Vérifier bouton "Annuler" présent

### Test 3: Membre Parti
1. Un membre quitte
2. Vérifier qu'il n'apparaît plus dans activeMembers
3. Vérifier qu'il apparaît toujours dans members (historique)
4. Vérifier left_at contient la date/heure

### Test 4: Après Départ
1. Membre quitte la colocation
2. ✅ Vérifier hasActiveColocation() = false
3. ✅ Vérifier peut créer nouvelle colocation
4. ✅ Vérifier peut accepter invitation

---

## 📁 Fichiers Modifiés/Créés

### Créés
- `resources/views/livewire/colocations/leave-button.blade.php`

### Modifiés
- `app/Models/User.php` - withPivot('left_at')
- `app/Models/Colocation.php` - withPivot('left_at')
- `resources/views/livewire/colocations/show.blade.php` - Ajout leave-button
- `app/Policies/ColocationPolicy.php` - Méthode leave()

---

## 💡 Différences Clés

### Départ vs Annulation

| Action | Qui | Effet | Status Colocation | left_at |
|--------|-----|-------|-------------------|---------|
| **Départ** | Member | Quitte | Reste active | now() |
| **Annulation** | Owner | Ferme | cancelled | null |

### Membre Actif vs Parti

| État | left_at | Visible dans | Peut agir |
|------|---------|--------------|-----------|
| **Actif** | null | activeMembers | ✅ |
| **Parti** | timestamp | members only | ❌ |

---

## 🎯 Résumé

**4 tâches accomplies sur 4** ✅

1. ✅ Table memberships (colocation_user) créée
2. ✅ Champ left_at ajouté et configuré
3. ✅ Départ membre implémenté
4. ✅ Owner empêché de quitter

**Système de memberships complet et fonctionnel !** 🎉
