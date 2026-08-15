# 02 — Architecture du Core

## 1. Points d'entrée

| Fichier | Contexte |
|---|---|
| `index.php` | Web. **Ne doit jamais être modifié** (mention explicite en en-tête). |
| `cmw` | CLI. Définit `$GLOBALS['CMW_ENV'] = 'standalone'` puis délègue aux builders. |
| `App/Bootstrap/standalone.php` | Bootstrap du contexte standalone (CLI). |

### Séquence web (`index.php`)

```php
require_once('App/Manager/Loader/Loader.php');

Loader::loadProject();      // autoloader CMW + env + vendor/autoload.php si présent
Loader::manageErrors();     // ErrorManager
Loader::loadAttributes();   // réflexion : collecte les #[Link] de tous les contrôleurs
Loader::loadRoutes();       // enregistrement des routes dans le Router
Loader::setLocale();        // i18n
Loader::loadInstall();      // bascule vers Installation/ si INSTALLSTEP !== -1
MaintenanceController::getInstance()->redirectMaintenance();
Loader::listenRouter();     // dispatch
```

## 2. Le Loader — `App/Manager/Loader/`

Deux fichiers : `Loader.php` et `AutoLoad.php`.

Méthodes notables :

| Méthode | Rôle |
|---|---|
| `loadProject()` | Init autoload + environnement |
| `loadAttributes()` / `listAttributes()` | Scan par réflexion des attributs de routing |
| `loadRoutes()` | Enregistre les routes collectées |
| `createSimpleRoute()` / `createSimpleRouteCallable()` | Déclaration de route programmatique (avec `weight`) |
| `loadImplementations(string $interface)` | Retourne toutes les implémentations d'une interface, tous packages confondus |
| `loadManagerImplementations($interface, string $managerName)` | Idem pour les implémentations de Managers |
| `getHighestImplementation(string $interface)` | Retourne l'implémentation de **poids le plus élevé** (`null` si aucune) |
| `loadLang()`, `setLocale()` | i18n |
| `loadComposer()` | Charge `vendor/autoload.php` **s'il existe** (Composer optionnel) |

### Autoload

L'autoloader maison mappe les namespaces `CMW\…` vers l'arborescence. Composer n'est **pas** requis
pour faire tourner le CMS.

## 3. Système d'implémentations pondéré ★

C'est **le** mécanisme d'extensibilité de CMW. Un package peut fournir une implémentation d'une
interface du Core ; celle dont le `weight()` est le plus élevé l'emporte.

**Emplacement attendu :**
```
App/Package/{Package}/Implementations/{Categorie}/{MonImplementation}.php
```

**Namespace attendu :**
```php
namespace CMW\Implementation\{Package}\{Categorie};
```

**Variante Manager :**
```
App/Package/{Package}/Implementations/Manager/{ManagerName}/{Impl}.php
namespace CMW\Implementation\{Package}\Manager\{ManagerName};
```

**Résolution :**
```php
$impl = Loader::getHighestImplementation(IMonInterface::class);
if ($impl !== null) {
    $impl->maMethode();
}
```

> ⚠️ `getHighestImplementation()` part d'un `$highestWeight = 1` et d'un `$index = 0` : si toutes les
> implémentations ont un poids ≤ 1, c'est **la première trouvée** qui est retournée. Les interfaces
> concernées doivent donc exposer une méthode `weight()`, et les implémentations concurrentes utiliser
> des poids > 1.

## 4. Routing — `App/Manager/Router/`

Basé sur **AltoRouter**.

```
Router/
├── Router.php          # cœur du routing
├── Link.php            # attribut #[Link]
├── LinkStorage.php     # collecte des routes découvertes
├── Route.php
├── Request.php
├── IRouter.php + Implementations/
└── RouterException.php
```

### Déclaration

```php
#[Link('/path', Link::GET, [], '/scope')]
#[Link('/users/@id', Link::POST, ['id' => '[0-9]+'], '/cmw-admin')]
private function methodName(): void { … }
```

- Signature : `#[Link(path, method, variables, scope)]`
- `variables` : tableau `nom => regex` pour les paramètres d'URL
- `scope` : préfixe de route ; le panel admin utilise `/cmw-admin/…`
- Les méthodes peuvent être `private` : elles sont invoquées par réflexion.

## 5. Base de données — `App/Manager/Database/DatabaseManager.php`

- PDO + requêtes préparées, connexion pilotée par les variables `DB_*` du `.env`.
- Un package peut ouvrir une **connexion secondaire** (cas de Litebans avec `LITEBANS_*`).
- Les Models étendent `App/Manager/Package/AbstractModel.php`.
- Les Entities étendent `App/Manager/Package/AbstractEntity.php` (propriétés typées).

Conventions SQL : voir [06-Conventions.md](06-Conventions.md).

## 6. Vues — `App/Manager/Views/View.php`

API fluide, implémente `IView` (donc surchargeable via le système d'implémentations).

```php
View::createAdminView('PackageName', 'viewName')
    ->addVariableList(['key' => $value])
    ->addVariable('other', $other)
    ->addStyle('Admin/Resources/Vendors/…/style.css')
    ->addScriptBefore('…/a.js')
    ->addScriptAfter('…/b.js')
    ->addPhpBefore('…')
    ->addPhpAfter('…')
    ->needAdminControl(true)
    ->view();

View::createPublicView('PackageName', 'viewName')->view();
View::basicPublicView('PackageName', 'viewName');   // raccourci
```

Autres options : `setCustomPath()`, `setCustomTemplate()`, `setOverrideBackendMode()`,
`loadInclude()` pour injecter des `beforeScript` / `afterScript` / `beforePhp` / `afterPhp` / `styles`.

**Résolution des vues publiques** : `View::createPublicView('News', 'main')` cherche le fichier dans le
**thème actif** (`Public/Themes/{Theme}/Views/News/main.view.php`), pas dans le package. C'est ce qui
permet à chaque thème de redéfinir le rendu de chaque package.

## 7. Inventaire des Managers — `App/Manager/`

| Manager | Rôle |
|---|---|
| `Api/` | `APIManager`, `APIRoute`, `PublicAPI` — dialogue avec l'API CMW (`APIURL`) |
| `Cache/` | `SimpleCacheManager` — cache fichier (`App/Storage/Cache`) |
| `Class/` | `PackageManager` |
| `Components/` | `ComponentsManager`, `IComponent` + `Base/` (Button, Div, Form, Header, Heading, Hr, Input, Label, Select, SelectOption, Textarea) |
| `Database/` | `DatabaseManager` (PDO) |
| `Download/` | Téléchargement de packages/thèmes depuis le market |
| `Editor/` | `EditorManager` (éditeur WYSIWYG) |
| `Env/` | `EnvManager` — lecture/écriture du `.env` |
| `Error/` | Gestion et affichage des erreurs |
| `Events/` | `AbstractEvent`, `Emitter`, `Listener` |
| `Facade/` | `FacadeCreator` |
| `Files/` | `FilesManager`, `FilesException` |
| `Filter/` | Filtrage / validation des entrées |
| `Flash/` | Messages flash (alertes utilisateur) |
| `Http/` | Utilitaires HTTP |
| `Lang/` | `LangManager` — traductions |
| `Loader/` | `Loader`, `AutoLoad` |
| `Mail/` | `MailManager` + vendor PHPMailer embarqué |
| `Manager/` | `AbstractManager` |
| `Metrics/` | `VisitsMetricsManager` + entity — statistiques de visites |
| `Notice/` | `WarningManager` — avertissements dans le dash |
| `Notification/` | Notifications |
| `Package/` | `AbstractController`, `AbstractModel`, `AbstractEntity`, `IPackageConfig(V2)`, `PackageMenuType`, `PackageSubMenuType`, `EntityType`, `GlobalObject`, `P` |
| `Permission/` | Permissions par rôle |
| `Requests/` | Requêtes entrantes |
| `Router/` | Routing |
| `Security/` | `EncryptManager`, `SecurityManager`, `HoneyInput`, `RateLimiter`, `HealthReport` |
| `Theme/` | Chargement, config, éditeur live, market, fichiers de thème (voir [04](04-Themes.md)) |
| `Twofa/` | `TwoFaManager` — TOTP |
| `Updater/` | `UpdatesManager`, `CMSUpdaterManager` |
| `Uploads/` | `ImagesManager` + formats |
| `Views/` | `View`, `IView` |
| `Webhook/` | Webhooks (Discord notamment) |
| `Xml/` | Manipulation XML (sitemap…) |

## 8. Événements

```php
Emitter::send(MonEvent::class, $data);
```

Les événements étendent `AbstractEvent`. Les listeners sont fournis par des implémentations de packages
(dossier `Events/` du package + implémentation correspondante).

## 9. Sécurité

| Mécanisme | Classe |
|---|---|
| Chiffrement des données sensibles (emails, tokens) | `EncryptManager` (sels `SALT`, `SALT_PASS`, `SALT_IV`) |
| Hash de mot de passe | BCrypt via `password_hash()` |
| 2FA TOTP | `TwoFaManager` |
| Rate limiting | `RateLimiter` |
| Anti-CSRF / anti-bot | `HoneyInput` |
| Permissions par rôle | `PermissionManager` — `UsersController::hasPermission('core.settings.website')` |
| Rapport de santé sécurité | `HealthReport` |

## 10. Mise à jour du CMS

`UpdatesManager` :

- `getVersion()` → `EnvManager::getValue('VERSION')`, ou `'DEV'` si absente ;
- `getCmwLatest()` → `PublicAPI::getData('cms/latest')` (ou `cms/latest/test` si `TEST_API_UPDATE`) ;
- `checkNewUpdateAvailable()` → compare version locale et distante ;
- `ignoreUpdates()` → vrai uniquement si `UPDATE_CHECKER=0` **et** `DEVMODE=1`.

`CMSUpdaterManager` récupère le lien de mise à jour via `POST /cms/update`
avec `current_version` + `target_version_id`.

> Note : `checkNewUpdateAvailable()` est appelée dans `Core\Package::menus()`, donc à chaque rendu de
> menu admin → un appel API par affichage de page admin. `getCmwLatest()` porte d'ailleurs un
> `@todo Cache this data`. Candidat d'optimisation pour la v1.

## 11. Installation

`Installation/` contient un installeur en 6 étapes (Welcome → Database → Website → Bundle →
AdminAccount → Finish), son `init.sql`, ses langs et ses vues. Le dossier est **supprimé après
installation**, sauf si `DEVMODE` est actif. `INSTALLSTEP=-1` signifie « installé ».

## 12. CLI — `App/Cli/`

```bash
php cmw               # menu interactif (CliBuilder)
php cmw theme-init    # ThemeBuilder → squelette de thème   ⚠️ CASSÉ (génère du V1)
php cmw package-init  # PackageBuilder → squelette de package ⚠️ CASSÉ (génère du V1)
php cmw ai-copilot    # AICopilotContextBuilder → contexte GitHub Copilot
```

Le CLI a ses propres langs (`App/Cli/Utils/Lang/{fr,en}.php`) et s'exécute en environnement
`standalone` (`.env.standalone`).

## 13. Utils — `App/Utils/`

Helpers statiques, namespace `CMW\Utils` :
`Arr`, `ArrayFormatter`, `ArrayMerger`, `Client`, `Date`, `Directory`, `File`, `Json`, `LazyValue`,
`Log`, `Number`, `Redirect`, `Str`, `Utils`, `Website`.
