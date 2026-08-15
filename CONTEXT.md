# CONTEXT — CraftMyWebsite (CMW)

> Fichier de contexte principal du projet. À lire en premier par tout agent IA / nouveau contributeur.
> Les documents détaillés sont dans [`Docs/`](Docs/README.md).

---

## 1. Le projet en une page

**CraftMyWebsite (CMW)** est un CMS PHP professionnel orienté **communautés gaming** (serveurs de jeux :
Minecraft, etc.), avec des briques e-commerce, un panel d'administration complet et un système
de packages/thèmes extensible.

| Info | Valeur |
|---|---|
| Statut | **BETA** — `v2.0.0-alpha` au README, déjà en production sur plusieurs sites |
| Objectif actuel | **Sortir la v1 stable** (projet repris après une période d'abandon) |
| Langue projet | **Français** (doc, commentaires, langs FR + EN). Le **code** s'écrit en anglais. |
| Licence | GNU GPL / CC BY-NC-ND 4.0 |
| Stack | PHP (plancher v1 : **8.4**), PDO/MySQL, TailwindCSS (cible **v4**, existant en v3), Flowbite |
| Front admin | TailwindCSS + Flowbite (thème `Dashboard`) |
| Front public | Thèmes indépendants (Tailwind utilisé par convention, pas obligatoire) |
| **Composer volontairement évité** | Le Core **doit s'en passer au maximum** (poids, dépendances) — libs vendorisées. `vendor/autoload.php` est chargé s'il existe ; un package tiers peut utiliser Composer s'il le souhaite. |

### Liens de référence

| Ressource | URL |
|---|---|
| Site officiel | https://craftmywebsite.fr/ |
| Documentation publique | https://craftmywebsite.fr/docs |
| GitHub public (org principale) | https://github.com/CraftMyWebsite |
| GitHub org pro | https://github.com/CraftMyWebsitePro |
| GitHub org communautaire | https://github.com/Overheat-Studio-Community |
| Repo Core | https://github.com/CraftMyWebsite/cmw-core |
| Repo Doc | https://github.com/CraftMyWebsite/cmw-doc |
| **Schéma JSON de toute la doc** | https://apiv2.craftmywebsite.fr/v1/docs/schem |
| Showcase composants UI du dash | https://dash.craftmywebsite.fr/ |
| Conventions | https://craftmywebsite.fr/docs/fr/technical/conventions/general |
| Discord | https://craftmywebsite.fr/discord |
| DeepWiki (analyse auto du repo) | https://deepwiki.com/CraftMyWebsite/cmw-core |

> Le schéma de doc renvoie un JSON `{LANG: {Section: {Catégorie: [{path, slug, title, editUrl}]}}}`.
> Utile pour retrouver/fetcher une page de doc précise sans naviguer sur le site.

### Contextes hors périmètre de ce fichier

Ce document couvre **le contexte public** : Core du CMS, packages, thèmes, thème Dashboard.
Les repos **API (`apiv2`), site vitrine, back-office interne** (org `CraftMyWebsitePro`) seront traités
dans un contexte séparé.

---

## 1 bis. Décisions actées pour la v1 (14/08/2026)

| Sujet | Décision |
|---|---|
| **Source de vérité de la version** | Tout doit passer sur **`App/Package/Core/Package.php`**. Aujourd'hui c'est `.cmw-version` qui fait foi à l'installation → à migrer. |
| **Périmètre v1** | **Tous les packages officiels** (org `CraftMyWebsite`). Les packages Overheat Studio ne sont **pas officiels** mais restent sur le Marketplace. |
| **Compatibilité ascendante** | Non bloquante — encore en beta, une réinstallation complète est acceptable ; des migrations de données seront proposées. |
| **E-commerce** | `package-shop` existe mais est expérimental → **seconde partie**, hors v1 initiale. |
| **PHP** | Plancher **8.4**. |
| **Composer** | Le Core l'évite au maximum. Modèle vendorisé assumé. |
| **Tests** | Hors scope immédiat, souhaitables à terme. |
| **`App/Manager/Components/`** | **POC** — sans rapport avec les composants du thème Dashboard. |
| **Legacy V1** (`IPackageConfig`, `IThemeConfig`) | **Support coupé**. |
| **`robots.txt` / `sitemap.xml`** | **Auto-générés à l'installation** → volontairement non versionnés. |
| **Documentation** | `cmw-doc` mis à jour **à chaque modification**. |
| **Ordre de travail** | 1) cartographier · 2) recenser les soucis · 3) traiter les issues GitHub · 4) roadmap v1 détaillée. |

Détail et travaux associés : [`Docs/08-Roadmap-V1.md`](Docs/08-Roadmap-V1.md).

---

## 2. Modèle de dépôts — point crucial

Le CMS fonctionne avec un **Core**, et des **packages / thèmes indépendants hébergés dans des repos séparés**.

### Intégrés au repo Core (`cmw-core`)

- `App/Package/Core` — cœur système : settings, menus, thèmes, packages, updates, maintenance, mails
- `App/Package/Users` — auth, rôles, permissions, 2FA
- `App/Package/Pages` — pages statiques
- `Public/Themes/Sampler` — thème public par défaut, installé de base

Tout le reste de `App/Package/*` et `Public/Themes/*` est **exclu par `.gitignore`** et vit dans son propre repo Git.

### Repos externes présents en local

**Packages — org `CraftMyWebsite`**

| Package | Repo |
|---|---|
| Faq | `CraftMyWebsite/package-faq` |
| Media | *(dépôt privé)* |
| Minecraft | `CraftMyWebsite/package-minecraft` |
| News | `CraftMyWebsite/package-news` |
| Rcon | *(dépôt privé)* |
| Redirect | `CraftMyWebsite/package-redirect` |
| Votes | `CraftMyWebsite/package-votes` |
| Wiki | `CraftMyWebsite/package-wiki` |

**Packages — org `Overheat-Studio-Community`**

| Package | Repo |
|---|---|
| Litebans | `cmw-package-litebans` |
| NewsPro | *(dépôt privé, premium)* |
| OverApi | `cmw-package-overapi` |
| OverEnv | `cmw-package-OverEnv` |
| OverTranslations | *(dépôt privé, payant)* |
| SimpleCookies | `cmw-package-simple-cookies` |
| SitemapExplorer | `cmw-package-sitemap_explorer` |

**Thèmes**

| Thème | Repo | Rôle |
|---|---|---|
| Sampler | *(dans le Core)* | Thème public **par défaut**, livré avec le CMS |
| Wipe | `CraftMyWebsite/theme-wipe` | Thème public d'**exemple / référence** |
| Dashboard | *(dépôt privé, org `CraftMyWebsitePro`)* | **Thème du panel admin** — voir https://dash.craftmywebsite.fr/ |

> ⚠️ Conséquence pratique : une modification dans `App/Package/News/` **ne se commit pas** dans le repo Core.
> Il faut committer/pousser depuis le sous-dossier correspondant (`git -C App/Package/News ...`).
> Toujours vérifier dans quel repo on écrit avant de committer.

### `.ignored_packages` / `.ignored_themes`

Fichiers locaux (gitignorés) listant les packages/thèmes désactivés au chargement pour le dev local.

---

## 3. Architecture — vue rapide

```
cmw/
├── index.php                 # Point d'entrée web (ne jamais modifier)
├── cmw                       # Point d'entrée CLI
├── .env                      # Config d'environnement (gitignoré)
├── Admin/                    # Assets + vues globales du panel admin
│   ├── Tailwind/             # Config + input CSS Tailwind du dash
│   └── Resources/            # Assets compilés, vendors, vues
├── App/
│   ├── Bootstrap/            # standalone.php (contexte CLI)
│   ├── Cli/                  # Builders CLI (theme-init, package-init, ai-copilot)
│   ├── Manager/              # ★ LE CORE — ~34 managers (voir Docs/02)
│   ├── Package/              # Packages (Core, Users, Pages = intégrés ; reste = repos externes)
│   ├── Storage/              # Cache, Logs, Reports, Visits
│   └── Utils/                # Helpers statiques (Str, Arr, Date, File, Log, Redirect…)
├── Installation/             # Installeur pas-à-pas (supprimé après install sauf DEVMODE)
├── Public/
│   ├── Themes/               # Thèmes publics + thème Dashboard (admin)
│   └── Uploads/              # Fichiers uploadés
└── .copilot/Technical/       # Doc technique importée (gitignorée) — source de vérité détaillée
```

### Cycle de vie d'une requête (`index.php`)

```php
Loader::loadProject();      // Autoloader + env + composer optionnel
Loader::manageErrors();     // Handler d'erreurs
Loader::loadAttributes();   // Scan par réflexion des #[Link] sur les contrôleurs
Loader::loadRoutes();       // Enregistrement dans AltoRouter
Loader::setLocale();        // i18n
Loader::loadInstall();      // Redirige vers l'installeur si INSTALLSTEP != -1
MaintenanceController::getInstance()->redirectMaintenance();
Loader::listenRouter();     // Dispatch
```

### Concepts clés

1. **Routing par attributs** — `#[Link('/path', Link::GET, ['id' => '[0-9]+'], '/cmw-admin/scope')]`
   sur les méthodes de contrôleur, découvert par réflexion.
2. **Système d'implémentations pondéré** — les packages fournissent des implémentations d'interfaces du
   Core ; `Loader::getHighestImplementation(IX::class)` retient celle au poids le plus élevé.
   C'est le mécanisme d'extensibilité central de CMW.
3. **Événements** — `Emitter::send(EventClass::class, $data)`, listeners via implémentations.
4. **Vues** — `View::createAdminView(...)` / `View::createPublicView(...)` avec chaînage fluide.
5. **Thèmes configurables en live** — `EditorMenu` / `EditorValue` + attributs HTML `data-cmw="menuKey:themeKey"`
   pour le live builder du configurateur de thème.
6. **Permissions** — format `{package}.{feature}.{action}`, ex. `users.manage.edit`, `core.settings.website`.

---

## 4. Commandes utiles

```bash
# Formatage obligatoire avant toute PR
php pretty-php.phar *

# Compilation TailwindCSS du panel admin (watch)
npm run tw-core

# CLI CMW
php cmw                # menu interactif
php cmw theme-init     # ⚠️ génère un squelette de thème — CASSÉ (format V1), voir Docs/10-CLI.md
php cmw package-init   # ⚠️ génère un squelette de package — CASSÉ (format V1), voir Docs/10-CLI.md
php cmw ai-copilot     # (re)génère le contexte GitHub Copilot
```

---

## 5. Environnement (`.env`)

| Variable | Rôle |
|---|---|
| `DIR` | Racine absolue du projet |
| `APIURL` | Endpoint de l'API CMW |
| `SALT`, `SALT_PASS`, `SALT_IV` | Sels et IV de chiffrement (`EncryptManager`) |
| `CMW_KEY` | Clé d'identification du site auprès de l'API |
| `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USERNAME`, `DB_PASSWORD` | Connexion MySQL |
| `PATH_SUBFOLDER`, `PATH_URL`, `PATH_ADMIN_VIEW` | Gestion des chemins / sous-dossier |
| `TIMEZONE` | Défaut `Europe/Paris` |
| `LOCALE` | Langue par défaut |
| `DEVMODE` | Mode développement |
| `UPDATE_CHECKER` | Vérification des mises à jour (`0` + `DEVMODE=1` ⇒ désactivée) |
| `INSTALLSTEP` | Étape d'installation (`-1` = installé) |
| `VERSION` | Version locale du CMS (`'DEV'` si absente) |
| `TEST_API_UPDATE`, `IS_RECETTE` | Bascules API de test / recette |
| `LITEBANS_*` | Connexion BDD secondaire du package Litebans |

`.env.standalone` : variables pour le contexte CLI (`$GLOBALS['CMW_ENV'] = 'standalone'`).

---

## 6. Conventions à respecter systématiquement

- **Namespaces** : toujours préfixés `CMW\` — voir [`Docs/06-Conventions.md`](Docs/06-Conventions.md)
- **BDD** : `snake_case`, préfixe `cmw_`, noms **en anglais**, colonnes préfixées du nom de table
- **Classes** : `{Feature}Controller`, `{Feature}Model`, `{Feature}Entity`
- **Textes UI** : jamais en dur → `LangManager::translate('package.key')`
- **Formatage** : `php pretty-php.phar *` avant chaque PR
- **Git** : dev sur `dev`, PR `dev` → `main`

---

## 7. Règles de travail avec l'assistant

- **Réponses en français.**
- **L'upload et le monitoring ne sont jamais gérés par l'assistant** — toujours demander à l'utilisateur de s'en charger.
- Vérifier le **repo cible** avant tout commit (Core vs package vs thème).
- La doc technique détaillée locale est dans `.copilot/Technical/` (gitignorée) — la consulter avant
  d'inventer un pattern.
- **Toute modification fonctionnelle doit être répercutée dans `cmw-doc`.**
- En cas de **changement cassant** sur le Core, faire la passe de refacto sur **tous les packages
  présents en local** dans la foulée. Sinon, travailler au cas par cas.
- Ne pas modifier `index.php` (mention explicite dans le fichier).

---

## 8. Sommaire de `Docs/`

| Document | Contenu |
|---|---|
| [`Docs/README.md`](Docs/README.md) | Index |
| [`Docs/01-Ecosysteme.md`](Docs/01-Ecosysteme.md) | Repos, orgs, liens, workflow multi-repo |
| [`Docs/02-Core-Architecture.md`](Docs/02-Core-Architecture.md) | Managers, cycle de vie, routing, DB, vues, events |
| [`Docs/03-Packages.md`](Docs/03-Packages.md) | Anatomie d'un package + inventaire |
| [`Docs/04-Themes.md`](Docs/04-Themes.md) | Anatomie d'un thème, configurateur, `data-cmw` |
| [`Docs/05-Dashboard-UI.md`](Docs/05-Dashboard-UI.md) | Composants UI du panel admin |
| [`Docs/06-Conventions.md`](Docs/06-Conventions.md) | Conventions code, namespaces, BDD, i18n |
| [`Docs/07-Workflow-Dev.md`](Docs/07-Workflow-Dev.md) | Env local, commandes, Git, PR |
| [`Docs/08-Roadmap-V1.md`](Docs/08-Roadmap-V1.md) | Décisions actées + chantiers pour la v1 stable |
| [`Docs/09-Issues-GitHub.md`](Docs/09-Issues-GitHub.md) | Snapshot et tri des 52 issues ouvertes |
| [`Docs/10-CLI.md`](Docs/10-CLI.md) | Le CLI `php cmw` : commandes, architecture, état réel |
| [`Docs/11-Extension-JetBrains.md`](Docs/11-Extension-JetBrains.md) | Le plugin IDE CMW (PhpStorm/IntelliJ) |
