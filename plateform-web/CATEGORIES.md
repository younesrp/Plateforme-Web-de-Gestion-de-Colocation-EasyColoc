# Système de Catégories - EasyColoc

## ✅ Tâches Accomplies

### 1. ✅ Créer migration categories
**Migration:** `2026_02_24_122400_create_categories_table.php`

**Structure:**
```sql
- id
- colocation_id (FK colocations)
- name (varchar)
- color (varchar, default: '#3B82F6')
- created_at
- updated_at
```

**Status:** ✅ Table créée et migrée

---

### 2. ✅ CRUD catégories (Owner only)
**Composant:** `resources/views/livewire/categories/manage.blade.php`

**Fonctionnalités:**

#### Create (Créer)
- Formulaire avec nom et couleur
- Validation des champs
- Couleur par défaut: #3B82F6 (bleu)
- Sélecteur de couleur visuel
- **Réservé à l'owner** (policy)

#### Read (Lire)
- Liste de toutes les catégories
- Affichage nom + pastille de couleur
- Visible par tous les membres
- Ordre: plus récentes en premier

#### Update (Modifier)
- Bouton "Modifier" sur chaque catégorie
- Pré-remplissage du formulaire
- Bouton "Annuler" pour annuler l'édition
- **Réservé à l'owner** (policy)

#### Delete (Supprimer)
- Bouton "Supprimer" sur chaque catégorie
- Confirmation avant suppression
- **Réservé à l'owner** (policy)

**Status:** ✅ CRUD complet avec protection owner

---

## 📊 Modèle Category

### Attributs
```php
protected $fillable = ['colocation_id', 'name', 'color'];
```

### Relations
```php
public function colocation(): BelongsTo
```

### Utilisation
```php
// Créer une catégorie
Category::create([
    'colocation_id' => $colocation->id,
    'name' => 'Courses',
    'color' => '#10B981',
]);

// Récupérer les catégories d'une colocation
$categories = $colocation->categories;

// Modifier une catégorie
$category->update(['name' => 'Nouveau nom']);

// Supprimer une catégorie
$category->delete();
```

---

## 🛡️ Policy CategoryPolicy

### Permissions

| Action | Admin | Owner | Member | User |
|--------|-------|-------|--------|------|
| viewAny | ✅ | ✅ | ✅ | ✅ |
| view | ✅ | ✅ | ✅ (si membre) | ❌ |
| create | ✅ | ✅ | ❌ | ❌ |
| update | ✅ | ✅ | ❌ | ❌ |
| delete | ✅ | ✅ | ❌ | ❌ |

### Règles
- **Admin** : Accès total (via BasePolicy)
- **Owner** : CRUD complet sur ses catégories
- **Member** : Lecture uniquement
- **Banned** : Aucun accès (via BasePolicy)

---

## 🎨 Interface Utilisateur

### Formulaire (Owner uniquement)
```
┌─────────────────────────────────────────────┐
│ Nom: [_____________________]  Couleur: [🎨] │
│                                              │
│ [Ajouter] ou [Modifier] [Annuler]           │
└─────────────────────────────────────────────┘
```

### Liste des Catégories
```
┌─────────────────────────────────────────────┐
│ 🟦 Courses          [Modifier] [Supprimer]  │
│ 🟩 Loyer            [Modifier] [Supprimer]  │
│ 🟨 Électricité      [Modifier] [Supprimer]  │
└─────────────────────────────────────────────┘
```

### Affichage Member (non-owner)
```
┌─────────────────────────────────────────────┐
│ 🟦 Courses                                   │
│ 🟩 Loyer                                     │
│ 🟨 Électricité                               │
└─────────────────────────────────────────────┘
```

---

## 🔧 Intégration

### Dans la vue colocation
Le composant est intégré dans `colocations/show.blade.php` :
- Affiché sous les invitations
- Séparé par une bordure
- Visible uniquement si colocation active

### Emplacement
```blade
@if($colocation->isActive())
    <div class="mt-8 pt-6 border-t">
        <livewire:categories.manage :colocation="$colocation" />
    </div>
@endif
```

---

## 🧪 Tests

### Test 1: Création (Owner)
1. Se connecter en tant qu'owner
2. Accéder à la colocation
3. Voir le formulaire "Catégories"
4. Entrer nom: "Courses"
5. Choisir couleur: vert
6. Cliquer "Ajouter"
7. ✅ Vérifier catégorie ajoutée
8. ✅ Vérifier formulaire réinitialisé

### Test 2: Modification (Owner)
1. Cliquer "Modifier" sur une catégorie
2. ✅ Vérifier formulaire pré-rempli
3. Modifier le nom
4. Cliquer "Modifier"
5. ✅ Vérifier catégorie mise à jour
6. ✅ Vérifier formulaire réinitialisé

### Test 3: Annulation Modification
1. Cliquer "Modifier"
2. Modifier les champs
3. Cliquer "Annuler"
4. ✅ Vérifier formulaire réinitialisé
5. ✅ Vérifier catégorie non modifiée

### Test 4: Suppression (Owner)
1. Cliquer "Supprimer"
2. Confirmer
3. ✅ Vérifier catégorie supprimée

### Test 5: Lecture (Member)
1. Se connecter en tant que member
2. Accéder à la colocation
3. ✅ Vérifier liste des catégories visible
4. ✅ Vérifier formulaire absent
5. ✅ Vérifier boutons "Modifier/Supprimer" absents

### Test 6: Protection Policy
1. En tant que member, tenter d'accéder directement
2. ✅ Vérifier blocage par policy

---

## 💡 Cas d'Usage

### Catégories Typiques
- 🏠 **Loyer** - #EF4444 (rouge)
- ⚡ **Électricité** - #F59E0B (orange)
- 💧 **Eau** - #3B82F6 (bleu)
- 🌐 **Internet** - #8B5CF6 (violet)
- 🛒 **Courses** - #10B981 (vert)
- 🍕 **Restaurants** - #F97316 (orange foncé)
- 🧹 **Ménage** - #06B6D4 (cyan)
- 🎬 **Loisirs** - #EC4899 (rose)

### Workflow
1. Owner crée les catégories de base
2. Membres voient les catégories disponibles
3. Catégories utilisées pour les dépenses (future feature)
4. Owner peut ajuster/supprimer selon besoins

---

## 📁 Fichiers Créés/Modifiés

### Créés (4)
1. `database/migrations/2026_02_24_122400_create_categories_table.php`
2. `app/Models/Category.php`
3. `app/Policies/CategoryPolicy.php`
4. `resources/views/livewire/categories/manage.blade.php`

### Modifiés (2)
1. `app/Models/Colocation.php` - Relation categories()
2. `app/Providers/AuthServiceProvider.php` - Policy enregistrée
3. `resources/views/livewire/colocations/show.blade.php` - Composant ajouté

---

## 🎯 Résumé

**2 tâches accomplies sur 2** ✅

1. ✅ Migration categories créée et exécutée
2. ✅ CRUD complet réservé à l'owner

### Fonctionnalités
- ✅ Création avec nom et couleur
- ✅ Modification inline
- ✅ Suppression avec confirmation
- ✅ Lecture pour tous les membres
- ✅ Protection par policy (owner only)
- ✅ Interface intuitive avec sélecteur de couleur

**Système de catégories opérationnel !** 🎉
