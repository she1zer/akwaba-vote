# Améliorations AKWA_25

## 📋 Résumé des changements

### 🗄️ Base de données — nouvelle migration
**Fichier :** `database/migrations/2026_05_25_000002_enhance_akwa_tables.php`

**Table `candidats` — nouvelles colonnes :**
| Colonne | Type | Description |
|---|---|---|
| `slug` | string, nullable | Identifiant URL unique auto-généré |
| `bio` | text, nullable | Présentation du candidat (1000 car.) |
| `slogan` | string(200), nullable | Slogan de campagne |
| `genre` | enum M/F/autre, nullable | Genre du candidat |
| `contact_email` | string, nullable | Email admin (non affiché) |
| `ordre` | unsignedInt | Ordre d'affichage |
| `is_active` | boolean, default true | Activer/désactiver sans supprimer |

**Table `talents` — nouvelles colonnes :**
| Colonne | Type | Description |
|---|---|---|
| `description` | text, nullable | Description de la catégorie |
| `couleur_hex` | string(7), default #cc0000 | Couleur spécifique par talent |
| `max_votes_par_ip` | unsignedInt, default 1 | Limite de votes par IP pour ce talent |

**Table `votes` — nouvelles colonnes :**
| Colonne | Type | Description |
|---|---|---|
| `device_fingerprint` | string(64), nullable | Empreinte navigateur (anti-fraude) |
| `score_confiance` | tinyInt 0-100 | Score de légitimité du vote |
| `pays` | char(2), nullable | Pays de l'IP |
| `is_flagged` | boolean, default false | Vote marqué comme suspect |
| `flag_reason` | string, nullable | Raison du marquage |

**Table `parametres` — nouvelles colonnes :**
| Colonne | Type | Description |
|---|---|---|
| `afficher_resultats_live` | boolean, default true | Masquer/afficher les résultats |
| `afficher_nb_votes` | boolean, default true | Masquer/afficher le nombre de votes |
| `couleur_primaire` | string(7) | Couleur principale de l'événement |
| `couleur_secondaire` | string(7) | Couleur secondaire |
| `lien_facebook` | string, nullable | URL page Facebook |
| `lien_instagram` | string, nullable | URL page Instagram |
| `reglement` | text, nullable | Règlement du vote |

**Nouvelles tables :**

- **`reactions`** — Réactions emoji sur les candidats (❤️ 🔥 ⭐ 👏), 1 par type/IP/heure
- **`stats_horaires`** — Cache précalculé pour les graphes d'activité horaire
- **`sessions_vote`** — Sessions nommées pour accès multi-points (ex: tables d'un gala)

---

### 🚀 Nouvelles fonctionnalités

#### Anti-fraude amélioré (`FraudeDetector.php`)
- Score de confiance 0–100 calculé automatiquement à chaque vote
- Détection : fréquence par IP, user-agent suspect (bots/curl), cadence trop rapide
- Votes suspects marqués `is_flagged = true` et visibles dans l'admin
- L'admin peut valider manuellement un vote suspect

#### Réactions post-vote
- Après avoir voté, l'utilisateur peut réagir (❤️ 🔥 ⭐ 👏) sur les candidats
- Limite : 1 réaction par type/IP/heure/candidat
- API : `POST /candidat/{id}/reaction`

#### Page Statistiques admin (`/admin/statistiques`)
- Graphique d'activité des votes sur 24h par heure
- Top 10 candidats toutes catégories
- Tableau des votes suspects avec action "Valider"
- Chiffres : votes valides, votes suspects, IPs suspectes, votes invalides

#### Export CSV amélioré
- BOM UTF-8 pour compatibilité Excel
- Nouvelle colonne : `% Talent` et `Votes suspects`
- Nouvel export : **CSV votes bruts** (`/admin/export/csv/bruts`) avec toutes les métadonnées

#### Candidats
- Champ `is_active` : désactiver un candidat sans le supprimer
- Toggle rapide dans la liste admin
- Affichage du nombre de votes dans la liste
- Slogan et bio affichés dans les vues

#### Talents
- Couleur personnalisable par catégorie
- Description de la catégorie
- Limite de votes par IP configurable par talent
- Réordonner via boutons ▲▼ dans la liste
- Nombre de votes affiché dans la liste

#### Paramètres étendus
- Masquer/afficher les résultats en temps réel
- Masquer/afficher le nombre de votes
- Personnalisation des couleurs
- Liens réseaux sociaux (Facebook, Instagram)
- Règlement du vote
- Affichage du temps restant avant fermeture

#### Partage social
- Boutons de partage sur la page résultats (Facebook, Instagram si configurés)
- Bouton "Copier le lien"

---

### 🛠️ Déploiement sur Railway

```bash
# 1. Pousser le code mis à jour
git add -A
git commit -m "feat: amélioration BDD, anti-fraude, réactions, statistiques"
git push origin main

# 2. Railway relancera automatiquement le build
# 3. Exécuter les nouvelles migrations via Railway shell :
php artisan migrate --force

# 4. Optionnel — re-seeder si environnement de test :
php artisan db:seed --class=AkwaSeeder --force
```

> **Note :** Les nouvelles colonnes ont toutes des valeurs par défaut, la migration est non-destructive.

---

### 📁 Fichiers modifiés / créés

**Nouveaux :**
- `database/migrations/2026_05_25_000002_enhance_akwa_tables.php`
- `app/Models/Reaction.php`
- `app/Models/StatsHoraire.php`
- `app/Models/SessionVote.php`
- `app/Services/FraudeDetector.php`
- `app/Http/Controllers/ReactionController.php`
- `app/Http/Controllers/Admin/StatistiqueController.php`
- `resources/views/admin/statistiques.blade.php`

**Modifiés :**
- `app/Models/Candidat.php` — nouveaux champs, scope `active()`, `reactionsCount()`
- `app/Models/Talent.php` — nouveaux champs, `candidatsActifs()`, `votesParHeure()`
- `app/Models/Vote.php` — nouveaux champs, scopes `valides()`, `flagges()`
- `app/Models/Parametre.php` — nouveaux champs, `tempsRestant()`
- `app/Services/ResultatService.php` — filtre votes flagués, stats horaires, détection fraude
- `app/Http/Controllers/Admin/AdminController.php` — stat votes_flagges
- `app/Http/Controllers/Admin/CandidatController.php` — toggle actif, votes count
- `app/Http/Controllers/Admin/TalentController.php` — reorder, votes count
- `app/Http/Controllers/Admin/ExportController.php` — CSV enrichi + CSV bruts
- `app/Http/Controllers/Admin/ParametreController.php` — nouveaux paramètres
- `app/Http/Controllers/VoteController.php` — intégration FraudeDetector
- `app/Http/Requests/CandidatRequest.php` — nouveaux champs
- `app/Http/Requests/TalentRequest.php` — nouveaux champs
- `routes/web.php` — nouvelles routes
- `database/seeders/AkwaSeeder.php` — nouveaux champs
- `resources/views/admin/dashboard.blade.php`
- `resources/views/admin/candidats/index.blade.php`
- `resources/views/admin/candidats/form.blade.php`
- `resources/views/admin/talents/index.blade.php`
- `resources/views/admin/talents/form.blade.php`
- `resources/views/admin/parametres.blade.php`
- `resources/views/public/resultats.blade.php`
