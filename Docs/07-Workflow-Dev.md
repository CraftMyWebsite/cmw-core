# 07 — Workflow de développement

## 1. Prérequis

| Élément | Version / précision |
|---|---|
| PHP | **8.4** (plancher acté pour la v1 — CI et Dockerfile alignés) |
| Extensions PHP | `mysqli`, `gd`, `curl`, `pdo`, `pdo_mysql`, `mbstring`, `zip` |
| Base de données | MariaDB ou MySQL |
| Node.js / npm | Pour la compilation TailwindCSS |
| IDE | PhpStorm (référence de la doc), IntelliJ, VSCode |

> Un **plugin JetBrains CMW** existe (PhpStorm / IntelliJ) : il génère packages, thèmes, managers et
> composants depuis le menu `New`. Voir [11-Extension-JetBrains.md](11-Extension-JetBrains.md)
> et https://plugins.jetbrains.com/plugin/26003-craftmywebsite.

> ⬜ La doc publique (`cmw-doc`) annonce encore « PHP 8.3 ou supérieur » — à passer à 8.4.

## 2. Environnement local

### Docker (fourni)

```bash
docker compose up -d
```

Services : `server` (PHP 8.3 + Apache, port 80), `db` (MariaDB, port 3306), `phpmyadmin` (port 8090).

### Sans Docker

Serveur web classique (Apache/Nginx) pointant sur la racine du projet.
Configurations fournies : `.htaccess` (Apache) et `.nginx` (Nginx).

### Configuration

Le fichier `.env` est **gitignoré** — voir la liste des variables dans [`../CONTEXT.md` § 5](../CONTEXT.md).

Pour développer confortablement :

```dotenv
DEVMODE=1
UPDATE_CHECKER=0   # les deux ensemble désactivent le vérificateur de mises à jour
```

`DEVMODE=1` empêche aussi la suppression du dossier `Installation/` après l'installation.

`INSTALLSTEP=-1` = site installé. Toute autre valeur redirige vers l'installeur.

## 3. Commandes

```bash
# Formatage — OBLIGATOIRE avant toute PR
php pretty-php.phar *

# TailwindCSS du panel admin (watch + minify)
npm run tw-core

# TailwindCSS d'un thème (depuis le dossier du thème)
npm run Sampler

# CLI CMW
php cmw               # menu interactif
php cmw theme-init    # squelette de thème    ⚠️ CASSÉ — voir Docs/10-CLI.md
php cmw package-init  # squelette de package  ⚠️ CASSÉ — voir Docs/10-CLI.md
php cmw ai-copilot    # régénère le contexte GitHub Copilot (.copilot/)
```

## 4. Git — multi-dépôts ★

**Avant chaque commit, identifier le bon dépôt.**

| Ce que je modifie | Dépôt |
|---|---|
| `App/Manager/`, `Admin/`, `Installation/`, `index.php`, `cmw` | **Core** (`cmw-core`) |
| `App/Package/Core`, `App/Package/Users`, `App/Package/Pages` | **Core** |
| `Public/Themes/Sampler` | **Core** |
| Tout autre `App/Package/{X}` | dépôt du package |
| `Public/Themes/Wipe`, `Public/Themes/Dashboard` | dépôt du thème |

```bash
# Statut d'un package externe
git -C App/Package/News status

# Commit dans un package externe
git -C App/Package/News add -A
git -C App/Package/News commit -m "[FIX] …"
```

`git status` à la racine **ne verra jamais** les modifications des dépôts externes.

### Branches

| Dépôt | Développement | Production |
|---|---|---|
| Core | `dev` | `main` |
| Packages / thèmes | `main` | `main` |

PR toujours **`dev` → `main`** sur le Core.

### Convention de messages de commit

Format observé sur l'historique du Core :

```
[FIX] Images rotation
[ADD] DeleteDirectory
[IMPROVES] Add multiple images dropper
```

Préfixes utilisés : `[FIX]`, `[ADD]`, `[IMPROVES]`.

## 5. CI

`.github/workflows/php.yml` — déclenché sur push/PR vers `main` **et `dev`** (hors `**.md`).

| Job | Contenu | Bloquant |
|---|---|---|
| `lint` | `php -l` sur `App`, `Admin`, `Installation`, `Public`, `index.php`, `cmw`. Échoue sur erreur de syntaxe **et sur toute dépréciation**. Matrice `ubuntu-latest` + `windows-latest`, PHP **8.4**. | ✅ oui |
| `format` | `pretty-php --check` (hors `Vendors`, `vendor`, `node_modules`) | ❌ non (`continue-on-error`) |

`php -l` renvoyant `0` même lorsqu'il émet une dépréciation, le job capture sa sortie et échoue
explicitement si elle n'est pas vide.

Le job `format` restera non bloquant tant que la dette de formatage (177 fichiers sur 319) n'est pas
résorbée — voir [08-Roadmap-V1.md § D](08-Roadmap-V1.md).

### Reproduire la CI en local

```bash
# Lint + dépréciations
find App Admin Installation Public index.php cmw -name '*.php' -type f -print0 \
  | xargs -0 -n1 -P4 php -l | grep -v '^No syntax errors'

# Formatage (pretty-php exige register_argc_argv)
php -d register_argc_argv=1 pretty-php.phar --check \
  -X '/\/(\.git|node_modules|Vendors|vendor)\//' App Admin Installation index.php
```

Templates d'issues : `.github/ISSUE_TEMPLATE/{bugReport,featureRequests}.md`.

## 6. Fichiers gitignorés notables

`.env`, `.env.**`, `.cmw-version`, `sitemap.xml`, `.idea/`, `.vscode/`, `.copilot/`,
`.ignored_packages`, `.ignored_themes`, `vendor/`, `composer.json`, `composer.lock`,
`node_modules/`, `package-lock.json`, `App/Storage/Logs/*`, `App/Storage/Cache/**/*.cache`,
`App/Storage/Reports/*.txt`, `Public/Uploads/*` (sauf quelques défauts).

## 7. Règles de collaboration avec l'assistant IA

- **Toujours répondre en français.**
- **Ne jamais gérer l'upload ni le monitoring** — demander systématiquement à Thomas de s'en charger.
- Vérifier le dépôt cible avant tout commit.
- Consulter `.copilot/Technical/` ou https://apiv2.craftmywebsite.fr/v1/docs/schem avant d'inventer un
  pattern.
- Ne pas modifier `index.php` (mention explicite dans le fichier).
