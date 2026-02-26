# Système de Gestion des Dépenses - EasyColoc

## ✅ Tâches Accomplies

### 1. ✅ Créer migration expenses
**Migration:** `2026_02_24_122500_create_expenses_table.php`

**Structure:**
```sql
- id
- colocation_id (FK colocations)
- category_id (FK categories, nullable, onDelete: set null)
- payer_id (FK users)
- title (varchar)
- amount (decimal 10,2)
- date (date)
- description (text, nullable)
- created_at, updated_at
```

**Status:** ✅ Table créée et migrée (417.12ms)

---

### 2. ✅ Implémenter ajout dépense
**Composant:** `expenses/manage.blade.php`

**Fonctionnalités:**
- Formulaire avec titre, montant, date, catégorie, description
- Validation des champs
- **Payeur automatique** = utilisateur connecté
- Catégorie optionnelle (dropdown)
- Description optionnelle
- Date par défaut = aujourd'hui
- Réinitialisation formulaire après ajout

**Validation:**
- Titre: requis, max 255 caractères
- Montant: requis, numérique, min 0.01
- Date: requise, format date
- Catégorie: optionnelle, doit exister
- Description: optionnelle, max 1000 caractères

**Status:** ✅ Ajout fonctionnel

---

### 3. ✅ Implémenter suppression dépense
**Implémenté dans:** `expenses/manage.blade.php`

**Règles de suppression:**
- **Payeur** peut supprimer sa propre dépense
- **Owner** peut supprimer n'importe quelle dépense
- Autres membres: aucun accès
- Confirmation avant suppression

**Protection:**
```php
if ($expense->payer_id !== Auth::id() && !$colocation->isOwner(Auth::user())) {
    abort(403);
}
```

**Status:** ✅ Suppression avec protection

---

### 4. ✅ Associer payeur
**Implémenté dans:** Modèle Expense + Composant

**Association:**
- Champ `payer_id` (FK users)
- Relation `payer()` dans le modèle
- **Payeur automatique** = Auth::id() lors de la création
- Affichage "Payé par {nom}" dans l'historique

**Relation:**
```php
public function payer(): BelongsTo
{
    return $this->belongsTo(User::class, 'payer_id');
}
```

**Status:** ✅ Payeur associé automatiquement

---

### 5. ✅ Historique dépenses
**Implémenté dans:** `expenses/manage.blade.php`

**Affichage:**
- Liste de toutes les dépenses
- Ordre: plus récentes en premier (latest('date'))
- Informations affichées:
  - Titre avec pastille de couleur (si catégorie)
  - Montant en gras
  - Payeur et date
  - Description (si présente)
  - Bouton supprimer (si autorisé)

**Chargement:**
- Eager loading: payer, category
- Optimisation des requêtes

**Status:** ✅ Historique complet

---

### 6. ✅ Filtre dépenses par mois
**Implémenté dans:** `expenses/manage.blade.php`

**Fonctionnalités:**
- Input type="month" pour sélection
- Filtre en temps réel (wire:model.live)
- Filtre par année ET mois
- Affichage de toutes les dépenses si aucun filtre

**Logique:**
```php
if ($this->month) {
    $query->whereYear('date', substr($this->month, 0, 4))
          ->whereMonth('date', substr($this->month, 5, 2));
}
```

**Status:** ✅ Filtre par mois fonctionnel

---

### 7. ✅ Statistiques par catégorie
**Implémenté dans:** `expenses/manage.blade.php`

**Calcul:**
- Somme des montants par catégorie
- Groupement par category_id
- Affichage "Sans catégorie" si null

**Affichage:**
- Nom de la catégorie + total
- Total général en bas
- Format: 2 décimales + symbole €
- Design: encadré avec fond gris

**Requête:**
```php
$stats = $colocation->expenses()
    ->selectRaw('category_id, SUM(amount) as total')
    ->groupBy('category_id')
    ->with('category')
    ->get();
```

**Status:** ✅ Statistiques par catégorie

---

## 📊 Modèle Expense

### Attributs
```php
protected $fillable = [
    'colocation_id', 
    'category_id', 
    'payer_id', 
    'title', 
    'amount', 
    'date', 
    'description'
];

protected $casts = [
    'amount' => 'decimal:2',
    'date' => 'date',
];
```

### Relations
```php
public function colocation(): BelongsTo
public function category(): BelongsTo
public function payer(): BelongsTo
```

---

## 🎨 Interface Utilisateur

### Formulaire d'Ajout
```
┌─────────────────────────────────────────────┐
│ Titre: [___________]  Montant: [_____] €    │
│ Date: [__________]    Catégorie: [▼______]  │
│ Description: [_________________________]     │
│                                              │
│ [Ajouter la dépense]                         │
└─────────────────────────────────────────────┘
```

### Filtre et Statistiques
```
┌─────────────────────────────────────────────┐
│ Filtrer par mois: [2026-02 ▼]               │
│                                              │
│ Statistiques par catégorie                   │
│ Courses ........................... 150.00 € │
│ Loyer ............................. 800.00 € │
│ Électricité ....................... 75.50 €  │
│ ──────────────────────────────────────────  │
│ Total ........................... 1025.50 €  │
└─────────────────────────────────────────────┘
```

### Historique
```
┌─────────────────────────────────────────────┐
│ 🟦 Courses Carrefour          150.00 €      │
│ Payé par Alice le 24/02/2026  [Supprimer]   │
│ Pain, lait, fruits                           │
├─────────────────────────────────────────────┤
│ 🟩 Loyer Février              800.00 €      │
│ Payé par Bob le 01/02/2026    [Supprimer]   │
└─────────────────────────────────────────────┘
```

---

## 🔒 Permissions

### Ajout de Dépense
- ✅ Tous les membres actifs
- ✅ Payeur = utilisateur connecté

### Suppression de Dépense
- ✅ Payeur de la dépense
- ✅ Owner de la colocation
- ❌ Autres membres

### Visualisation
- ✅ Tous les membres actifs
- ✅ Historique complet
- ✅ Statistiques

---

## 🧪 Tests

### Test 1: Ajout Dépense
1. Se connecter en tant que membre
2. Accéder à la colocation
3. Remplir formulaire:
   - Titre: "Courses"
   - Montant: 50.00
   - Date: aujourd'hui
   - Catégorie: Courses
4. Cliquer "Ajouter"
5. ✅ Vérifier dépense ajoutée
6. ✅ Vérifier payeur = utilisateur connecté
7. ✅ Vérifier formulaire réinitialisé

### Test 2: Suppression (Payeur)
1. Ajouter une dépense
2. Cliquer "Supprimer"
3. Confirmer
4. ✅ Vérifier dépense supprimée

### Test 3: Suppression (Owner)
1. En tant qu'owner
2. Voir dépense d'un autre membre
3. Cliquer "Supprimer"
4. ✅ Vérifier suppression autorisée

### Test 4: Suppression (Autre Membre)
1. En tant que membre (non-payeur, non-owner)
2. Voir dépense d'un autre
3. ✅ Vérifier bouton "Supprimer" absent

### Test 5: Filtre par Mois
1. Ajouter dépenses sur plusieurs mois
2. Sélectionner un mois
3. ✅ Vérifier seules les dépenses du mois affichées
4. Effacer le filtre
5. ✅ Vérifier toutes les dépenses affichées

### Test 6: Statistiques
1. Ajouter dépenses dans différentes catégories
2. ✅ Vérifier sommes par catégorie
3. ✅ Vérifier total général
4. ✅ Vérifier "Sans catégorie" si applicable

### Test 7: Catégorie Optionnelle
1. Ajouter dépense sans catégorie
2. ✅ Vérifier dépense créée
3. ✅ Vérifier pas de pastille de couleur
4. ✅ Vérifier dans stats "Sans catégorie"

---

## 💡 Cas d'Usage

### Scénario Typique
1. Alice paie les courses: 50€
2. Bob paie le loyer: 800€
3. Charlie paie l'électricité: 75€
4. Tous voient l'historique complet
5. Statistiques montrent répartition
6. Filtre par mois pour voir dépenses mensuelles

### Workflow
1. Membre paie une dépense
2. Ajoute la dépense dans l'app
3. Choisit catégorie appropriée
4. Autres membres voient la dépense
5. Statistiques mises à jour automatiquement
6. Owner peut supprimer si erreur

---

## 📁 Fichiers Créés/Modifiés

### Créés (3)
1. `database/migrations/2026_02_24_122500_create_expenses_table.php`
2. `app/Models/Expense.php`
3. `resources/views/livewire/expenses/manage.blade.php`

### Modifiés (2)
1. `app/Models/Colocation.php` - Relation expenses()
2. `resources/views/livewire/colocations/show.blade.php` - Composant ajouté

---

## 🎯 Résumé

**7 tâches accomplies sur 7** ✅

1. ✅ Migration expenses créée et exécutée
2. ✅ Ajout dépense avec validation
3. ✅ Suppression avec protection (payeur/owner)
4. ✅ Payeur associé automatiquement
5. ✅ Historique complet avec détails
6. ✅ Filtre par mois en temps réel
7. ✅ Statistiques par catégorie avec total

### Fonctionnalités Clés
- ✅ Formulaire complet et intuitif
- ✅ Validation stricte
- ✅ Permissions granulaires
- ✅ Filtre temps réel
- ✅ Statistiques automatiques
- ✅ Interface responsive
- ✅ Eager loading optimisé

**Système de dépenses complet et opérationnel !** 🎉
