# Système d'Invitations - EasyColoc

## ✅ Tâches Accomplies

### 1. ✅ Créer table invitations
**Migration:** `2026_02_24_122300_create_invitations_table.php`

**Structure:**
```sql
- id
- colocation_id (FK colocations)
- email (varchar)
- token (varchar, unique)
- status (varchar, default: 'pending')
- accepted_at (timestamp, nullable)
- refused_at (timestamp, nullable)
- created_at
- updated_at
```

**Status:** ✅ Table créée (migration prête)

---

### 2. ✅ Générer token unique
**Implémenté dans:** `app/Models/Invitation.php`

**Méthode:**
```php
public static function generateToken(): string
{
    return Str::random(32);
}
```

**Utilisation:**
- Token de 32 caractères aléatoires
- Unique dans la table (contrainte DB)
- Généré automatiquement lors de la création

**Status:** ✅ Génération automatique de token unique

---

### 3. ✅ Envoyer email invitation
**Fichiers créés:**
- `app/Mail/InvitationMail.php` - Classe Mailable
- `resources/views/emails/invitation.blade.php` - Template email
- `resources/views/livewire/invitations/send.blade.php` - Composant envoi

**Fonctionnalités:**
- Formulaire d'envoi (email)
- Validation email
- Création invitation avec token
- Envoi email avec lien d'invitation
- Liste des invitations en attente
- Protection par policy (owner uniquement)

**Email contient:**
- Nom de la colocation
- Description
- Lien vers l'invitation (avec token)

**Status:** ✅ Envoi d'email fonctionnel

---

### 4. ✅ Implémenter acceptation invitation
**Composant:** `resources/views/livewire/invitations/show.blade.php`

**Fonctionnalités:**
- Affichage détails colocation
- Bouton "Accepter l'invitation"
- Vérifications avant acceptation:
  - Invitation pending
  - Email correspond
  - Pas de colocation active
- Ajout comme membre
- Mise à jour status = 'accepted'
- Enregistrement accepted_at
- Redirection vers colocation

**Status:** ✅ Acceptation fonctionnelle avec vérifications

---

### 5. ✅ Implémenter refus invitation
**Implémenté dans:** `resources/views/livewire/invitations/show.blade.php`

**Fonctionnalités:**
- Bouton "Refuser"
- Mise à jour status = 'refused'
- Enregistrement refused_at
- Message de confirmation
- Redirection vers dashboard

**Status:** ✅ Refus fonctionnel

---

### 6. ✅ Vérifier correspondance email
**Implémenté dans:** `resources/views/livewire/invitations/show.blade.php`

**Vérifications:**
```php
if (Auth::user()->email !== $this->invitation->email) {
    $this->error = 'Cette invitation n\'est pas pour vous.';
}
```

**Protections:**
- Vérification au mount()
- Vérification avant accept()
- Message d'erreur si non-correspondance
- Blocage des actions

**Status:** ✅ Vérification email stricte

---

### 7. ✅ Bloquer si user a déjà colocation active
**Implémenté dans:** `resources/views/livewire/invitations/show.blade.php`

**Vérifications:**
```php
if (Auth::user()->hasActiveColocation()) {
    $this->error = 'Vous avez déjà une colocation active.';
}
```

**Protections:**
- Vérification au mount()
- Vérification avant accept()
- Message d'erreur explicite
- Blocage de l'acceptation

**Status:** ✅ Blocage multi-colocation actif

---

## 📊 Flux d'Invitation

### 1. Envoi (Owner)
1. Owner accède à sa colocation
2. Remplit le formulaire d'invitation (email)
3. Clique "Envoyer l'invitation"
4. Système crée invitation avec token unique
5. Email envoyé avec lien d'invitation
6. Invitation apparaît dans "En attente"

### 2. Réception (Invité)
1. Reçoit email d'invitation
2. Clique sur le lien
3. Redirigé vers `/invitations/{token}`
4. Voit détails de la colocation

### 3. Acceptation
**Vérifications:**
- ✅ Invitation pending
- ✅ Email correspond
- ✅ Pas de colocation active

**Actions:**
- Status → 'accepted'
- accepted_at → now()
- Ajout comme membre
- Redirection vers colocation

### 4. Refus
**Actions:**
- Status → 'refused'
- refused_at → now()
- Message confirmation
- Redirection dashboard

---

## 🔧 Modèle Invitation

### Relations
```php
public function colocation(): BelongsTo
```

### Méthodes
```php
generateToken()      // Génère token unique
isPending()          // Vérifie si pending
accept()             // Accepte l'invitation
refuse()             // Refuse l'invitation
```

### Status
- `pending` - En attente
- `accepted` - Acceptée
- `refused` - Refusée

---

## 🛡️ Sécurité

### Vérifications Email
- Email doit correspondre exactement
- Vérification au chargement
- Vérification avant acceptation
- Message d'erreur si non-correspondance

### Blocage Multi-Colocation
- Vérification via `hasActiveColocation()`
- Blocage au chargement
- Blocage avant acceptation
- Message d'erreur explicite

### Protection Owner
- Seul l'owner peut inviter
- Policy `addMember` vérifiée
- Formulaire masqué pour non-owners

### Token Unique
- 32 caractères aléatoires
- Contrainte unique en DB
- Non-devinable
- Utilisé dans l'URL

---

## 📧 Template Email

**Contenu:**
- Titre : "Invitation à rejoindre une colocation"
- Nom de la colocation
- Description (si présente)
- Bouton "Voir l'invitation"
- Lien : `route('invitations.show', $token)`

**Style:**
- HTML responsive
- Bouton CTA visible
- Design simple et clair

---

## 🧪 Tests

### Test 1: Envoi Invitation
1. Se connecter en tant qu'owner
2. Accéder à la colocation
3. Entrer un email
4. Cliquer "Envoyer l'invitation"
5. ✅ Vérifier message succès
6. ✅ Vérifier email reçu
7. ✅ Vérifier invitation dans liste

### Test 2: Acceptation Valide
1. Recevoir invitation
2. Cliquer sur lien
3. Se connecter avec email correspondant
4. Cliquer "Accepter"
5. ✅ Vérifier ajout comme membre
6. ✅ Vérifier status = 'accepted'
7. ✅ Vérifier redirection

### Test 3: Email Non-Correspondant
1. Recevoir invitation pour email A
2. Se connecter avec email B
3. Accéder au lien
4. ✅ Vérifier message d'erreur
5. ✅ Vérifier boutons désactivés

### Test 4: Colocation Active
1. Avoir une colocation active
2. Recevoir invitation
3. Accéder au lien
4. ✅ Vérifier message d'erreur
5. ✅ Vérifier impossibilité d'accepter

### Test 5: Refus
1. Recevoir invitation
2. Accéder au lien
3. Cliquer "Refuser"
4. ✅ Vérifier status = 'refused'
5. ✅ Vérifier redirection dashboard

### Test 6: Invitation Déjà Traitée
1. Accepter/refuser une invitation
2. Réaccéder au lien
3. ✅ Vérifier message "plus valide"

---

## 📁 Fichiers Créés

### Migrations (1)
- `database/migrations/2026_02_24_122300_create_invitations_table.php`

### Modèles (1)
- `app/Models/Invitation.php`

### Mail (1)
- `app/Mail/InvitationMail.php`

### Vues (3)
- `resources/views/emails/invitation.blade.php`
- `resources/views/livewire/invitations/send.blade.php`
- `resources/views/livewire/invitations/show.blade.php`

### Routes (1)
- `routes/web.php` (modifié)

### Relations (1)
- `app/Models/Colocation.php` (modifié - relation invitations)

---

## 🚀 Routes

```
GET  /invitations/{token}  - Afficher invitation (auth)
POST /invitations/send     - Envoyer invitation (Livewire)
POST /invitations/accept   - Accepter invitation (Livewire)
POST /invitations/refuse   - Refuser invitation (Livewire)
```

---

## 💡 Utilisation

### Envoyer une invitation
```php
$invitation = Invitation::create([
    'colocation_id' => $colocation->id,
    'email' => 'user@example.com',
    'token' => Invitation::generateToken(),
    'status' => 'pending',
]);

Mail::to($email)->send(new InvitationMail($invitation));
```

### Accepter une invitation
```php
$invitation->accept();
$colocation->members()->attach($userId);
```

### Refuser une invitation
```php
$invitation->refuse();
```

### Vérifier status
```php
if ($invitation->isPending()) {
    // Actions possibles
}
```

---

## 🎯 Résumé

**7 tâches accomplies sur 7** ✅

1. ✅ Table invitations créée
2. ✅ Token unique généré automatiquement
3. ✅ Email d'invitation envoyé
4. ✅ Acceptation implémentée avec vérifications
5. ✅ Refus implémenté
6. ✅ Vérification correspondance email
7. ✅ Blocage si colocation active

**Système d'invitations complet et sécurisé !** 🎉
