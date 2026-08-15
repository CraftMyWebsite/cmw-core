# 10 — CLI CMW (`php cmw`)

> ⚠️ **Avertissement — état au 15/08/2026**
> Les deux générateurs (`theme-init`, `package-init`) produisent du code au **format V1 obsolète**
> (`infos.json`) alors que le CMS attend `Theme.php` / `Package.php`. **Le code généré ne fonctionne
> pas en l'état.** Détail et vérification en [§ 6](#6-état-réel--ce-qui-est-cassé).
> Seule `ai-copilot` est opérationnelle.

---

## 1. Vue d'ensemble

Le CLI est le point d'entrée hors-web du CMS. Il vit à la racine (`cmw`, sans extension) et délègue à
des *builders* dans `App/Cli/`.

```bash
php cmw               # menu : liste des commandes disponibles
php cmw theme-init    # assistant de création de thème
php cmw package-init  # assistant de création de package
php cmw ai-copilot    # synchronise la doc technique dans .copilot/Technical
```

| Commande | Builder | État |
|---|---|---|
| *(aucune)* | `CliBuilder::emptyArgs()` | ✅ fonctionne |
| `theme-init` | `App/Cli/Builder/Theme/ThemeBuilder.php` | ❌ génère du V1 obsolète |
| `package-init` | `App/Cli/Builder/Package/PackageBuilder.php` | ❌ génère du V1 obsolète |
| `ai-copilot` | `App/Cli/Builder/AI/Copilot/AICopilotContextBuilder.php` | ⚠️ fonctionne, mais Unix seulement |

### Contraintes d'exécution

- **À lancer impérativement depuis la racine du projet.** Les `require_once` internes sont relatifs au
  répertoire courant : lancé ailleurs, le CLI meurt sur
  `Failed opening required 'App/Cli/CliBuilder.php'`.
- Nécessite un **`.env.standalone`** valide (voir § 3).
- Les assistants sont **interactifs** (`readline`), donc scriptables par pipe :
  `printf 'MonTheme\n1.0.0\nAuteur\nbeta-01\n1\n' | php cmw theme-init`.

---

## 2. Architecture

```
cmw                                   # point d'entrée : routage des commandes
App/Cli/
├── CliBuilder.php                    # classe de base : I/O terminal + chargement des langs
├── Utils/Lang/{fr,en}.php            # constantes de traduction du CLI
└── Builder/
    ├── Theme/
    │   ├── ThemeBuilder.php              # assistant (questions)
    │   └── ThemeBuilderInstallation.php  # génération des fichiers
    ├── Package/
    │   ├── PackageBuilder.php            # assistant (questions)
    │   └── PackageBuilderGeneration.php  # génération des fichiers
    └── AI/Copilot/
        └── AICopilotContextBuilder.php
```

Chaque builder **étend `CliBuilder`** et fait tout son travail dans son **constructeur** :
il n'y a ni dispatcher ni méthode `run()`.

### `CliBuilder` — API disponible

| Méthode | Rôle |
|---|---|
| `say(string ...$contents)` | Écrit sans saut de ligne (ajoute une espace après chaque élément) |
| `sayLn(string ...$contents)` | Écrit chaque élément entouré de sauts de ligne |
| `read(): ?string` | Lit une saisie utilisateur via `readline('> ')` |
| `loadLang()` *(privé)* | Charge `App/Cli/Utils/Lang/{LOCALE}.php` |

Les textes sont des **constantes globales** (`CLI_THEME_BUILDER_NAME`, `CLI_EMPTY_ARGS`…), pas des clés
`LangManager`. C'est le seul endroit du projet qui fonctionne ainsi.

---

## 3. Environnement standalone

Le fichier `cmw` pose `$GLOBALS['CMW_ENV'] = 'standalone'` **avant** tout chargement :

```php
$GLOBALS['CMW_ENV'] = 'standalone';
include_once('App/Manager/Env/EnvManager.php');
```

`EnvManager::setFileName()` bascule alors sur **`.env.standalone`** au lieu de `.env` :

```php
if (isset($GLOBALS['CMW_ENV']) && $GLOBALS['CMW_ENV'] === 'standalone') {
    $this->envFileName = '.env.standalone';
    return;
}
```

Conséquence : le CLI lit une configuration **distincte** de celle du site. Les deux fichiers sont
gitignorés et doivent être maintenus en parallèle (`DIR`, `LOCALE`, accès BDD…).

### `App/Bootstrap/standalone.php` — code mort

Ce fichier prétend être le bootstrap du contexte standalone :

```php
$GLOBALS['CMW_ENV'] = 'standalone';
error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_WARNING);
require_once('App/Manager/Loader/Loader.php');
Loader::loadProject();
Loader::manageErrors();
Loader::loadAttributes();
Loader::setLocale();
```

Mais **personne ne l'inclut** : `cmw` refait ce travail à sa manière. Un `grep` sur tout le projet ne
remonte aucun `require`/`include` de ce fichier. Soit le brancher, soit le supprimer.

---

## 4. `ai-copilot` — synchronisation de la doc

La seule commande réellement opérationnelle.

```bash
php cmw ai-copilot
```

Ce qu'elle fait :
1. clone (ou `git pull`) `https://github.com/CraftMyWebsite/cmw-doc.git` en `--depth=1`
   dans `sys_get_temp_dir() . '/cmw-doc'` ;
2. supprime `.copilot/Technical` s'il existe ;
3. copie `FR/Technical` du dépôt vers `.copilot/Technical`.

> ⚠️ **Unix uniquement** : la commande utilise `rm -rf` et `cp -r` via `shell_exec()`, indisponibles
> sur Windows. Elle ne récupère que **FR/Technical** — ni l'anglais, ni la section `Users`.

`.copilot/` est gitignoré : c'est un cache local destiné aux assistants IA.

---

## 5. Ajouter une commande

Le routage est une **suite de `if`** dans `cmw` :

```php
if ($command === 'ma-commande') {
    require_once(EnvManager::getInstance()->getValue('DIR') . 'App/Cli/Builder/…/MonBuilder.php');
    new MonBuilder();
}
```

Étapes :
1. créer `App/Cli/Builder/{Domaine}/MonBuilder.php`, namespace `CMW\Cli\Builder\{Domaine}` ;
2. étendre `CliBuilder`, appeler `parent::__construct()` puis faire le travail dans le constructeur ;
3. ajouter le `require_once` + `new` dans `cmw` ;
4. déclarer les textes dans `App/Cli/Utils/Lang/{fr,en}.php` **et** les référencer dans
   `CLI_EMPTY_ARGS` pour qu'ils apparaissent au menu.

> Le `require_once` en tête des builders (`require_once('App/Cli/CliBuilder.php')`) est **relatif**,
> d'où l'obligation de lancer le CLI depuis la racine.

---

## 6. État réel — ce qui est cassé

Constats **vérifiés par exécution** dans un bac à sable isolé, le 15/08/2026.

### 6.1 `theme-init`

Arborescence réellement produite :

```
Public/Themes/TestTheme/
├── infos.json          ❌ format V1 — le CMS attend Theme.php (IThemeConfigV2)
├── assets/             ❌ minuscule (convention : Assets/)
└── views/              ❌ minuscule (convention : Views/)
    ├── templates.php   ❌ devrait être template.php
    ├── Core/           ⚠️ créé via '/Views/' alors que le dossier parent est '/views/'
    ├── Includes/{footer,head,header}.inc.php
    ├── alerts/  errors/  pages/  users/   ❌ minuscules
```

| Problème | Détail |
|---|---|
| **Format V1** | Génère `infos.json` (`creator`, `name`, `version`, `cmwVersion`) au lieu d'un `Theme.php` implémentant `IThemeConfigV2`. Le thème n'est **pas reconnu** par `ThemeLoader`. |
| **Casse incohérente** | `createDirectories()` crée `$path.'/views'` puis `$path.'/Views/'.$item`. Sur macOS (FS insensible à la casse) tout atterrit dans `views/` ; **sur Linux cela crée deux dossiers distincts** `views/` et `Views/`. |
| **Fichiers manquants** | Ni `router.php`, ni `Config/config.settings.php`, ni `Resources/`, ni `package.json`. |
| `templates.php` | Le moteur attend `template.php` (`BaseViewImplementation` ligne 341). |
| **Extensions ignorées** | L'assistant demande Bootstrap / Tailwind / jQuery / FontAwesome, mais `generateExtensions()` et tous les `download*()` sont des **corps vides**. La réponse est collectée puis jetée. |
| **`RuntimeException` non importée** | Dans le namespace `CMW\Cli\Builder\Theme`, `throw new RuntimeException(...)` résout vers `CMW\Cli\Builder\Theme\RuntimeException` → `Error: Class not found` si un `mkdir` échoue. |
| `TODO` | « Check if the Theme name is not already use » — aucun contrôle d'écrasement d'un thème existant. |

### 6.2 `package-init`

Arborescence réellement produite :

```
App/Package/TestPkg/
├── infos.json                              ❌ format V1
├── Controllers/TestPkgController.php
├── Entities/TestPkgEntity.php
├── Implementations/TestPkgMenusImplementations.php   ❌ à plat (voir ci-dessous)
├── Init/init.sql
├── Lang/{en,fr}.php
├── Models/TestPkgModel.php
└── Views/demo.admin.view.php
```

| Problème | Détail |
|---|---|
| **Format V1** | `infos.json` au lieu de `Package.php` implémentant `IPackageConfigV2` → package **non chargé**. |
| **Implémentation jamais chargée** | Le fichier est écrit directement dans `Implementations/`, or `Loader::loadImplementations()` ne parcourt que les **sous-dossiers** (`if (!is_dir(...)) continue;`) et attend le namespace `CMW\Implementation\{Package}\{Categorie}`. Le fichier généré déclare `CMW\Implementation\{Package}` → jamais découvert. |
| **Permissions absentes** | `Init/Permissions.php` n'est pas généré (bloc commenté, `// TODO REWRITE THIS PART`) alors que le contrôleur généré appelle `redirectIfNotHavePermissions("core.dashboard", "demo.show")`. |
| **Dossiers manquants** | Ni `Interfaces/`, ni `Events/`, ni `Public/`, ni `Type/`, ni `Exception/`, ni `Init/uninstall.sql`. |
| **Vue en Bootstrap** | La vue générée utilise `d-flex`, `row`, `col-12 col-lg-3` — le panel admin est en **TailwindCSS** depuis la V2. |
| **Entité non conforme** | `{Package}Entity` n'étend pas `AbstractEntity`. |
| **Typage fragile** | `PackageBuilder::$menuType` est typé `int` mais reçoit `trim($this->read())` (string). La coercition sauve le cas nominal ; toute saisie non numérique lève un `TypeError`. |
| **Imports morts** | `PackageBuilderGeneration` importe `DatabaseManager`, `LangManager`, `Link`, `View` sans jamais les utiliser. |

### 6.3 Ce qu'il faudrait générer

Pour être utilisables, les générateurs doivent produire les structures décrites dans
[03-Packages.md](03-Packages.md) et [04-Themes.md](04-Themes.md) — c'est-à-dire, au minimum :

- **thème** → `Theme.php` (V2), `router.php`, `Config/config.settings.php`, `Assets/`, `Resources/`,
  `Views/template.php` + sous-dossiers en `Majuscule`, `package.json` **TailwindCSS 4** ;
- **package** → `Package.php` (V2) avec `PackageMenuType`, `Init/Permissions.php`,
  `Init/{init,uninstall}.sql`, `Interfaces/`, `Implementations/{Categorie}/`, vue admin en Tailwind.

En attendant, deux voies fiables :
1. **copier un module existant** — `Public/Themes/Sampler` pour un thème, `App/Package/Faq` pour un
   package simple ;
2. utiliser l'**extension JetBrains**, dont les générateurs produisent une arborescence bien plus
   proche de la cible (mais encore en interfaces V1) — voir
   [11-Extension-JetBrains.md](11-Extension-JetBrains.md).

---

## 7. Chantiers CLI pour la v1

| # | Chantier | Priorité |
|---|---|---|
| 1 | Réécrire `theme-init` : `Theme.php` V2 + arborescence conforme | ⬛ haute |
| 2 | Réécrire `package-init` : `Package.php` V2 + `Permissions.php` + `Implementations/{Categorie}/` | ⬛ haute |
| 3 | Corriger la casse des dossiers générés (bug silencieux sur Linux) | ⬛ haute |
| 4 | Générer les thèmes en **TailwindCSS 4** (voir [04-Themes.md](04-Themes.md)) | ⬜ moyenne |
| 5 | Rendre `ai-copilot` portable (remplacer `rm -rf`/`cp -r` par du PHP natif) | ⬜ moyenne |
| 6 | Brancher ou supprimer `App/Bootstrap/standalone.php` | ⬜ basse |
| 7 | Implémenter ou retirer le choix d'extensions du `theme-init` | ⬜ basse |
| 8 | Contrôler l'écrasement d'un module existant avant génération | ⬜ basse |
| 9 | Remplacer la chaîne de `if` de `cmw` par un vrai dispatcher | ⬜ basse |
| 10 | Mutualiser les templates avec l'**extension JetBrains**, qui dispose déjà d'un jeu complet | ⬜ basse |

> Le lien « LIENS DU WIKI CLI » affiché par `php cmw` est un **placeholder** jamais remplacé
> (`CLI_EMPTY_ARGS` dans `App/Cli/Utils/Lang/fr.php`). À faire pointer vers la doc réelle.
