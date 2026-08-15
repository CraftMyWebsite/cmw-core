# 03 — Packages

## 1. Principe

Un package est une brique fonctionnelle autonome, installable/désinstallable depuis le panel admin.
Trois packages sont **non supprimables** (`isCore() === true`) et livrés avec le Core : **Core**,
**Users**, **Pages**.

## 2. Anatomie

```
App/Package/{PackageName}/
├── Package.php          ★ obligatoire — métadonnées + menus (implémente IPackageConfigV2)
├── Controllers/         Routes (#[Link]) + logique HTTP — {Feature}Controller
├── Models/              Accès BDD — {Feature}Model extends AbstractModel
├── Entities/            Objets de données — {Feature}Entity extends AbstractEntity
├── Interfaces/          Interfaces exposées aux autres packages
├── Implementations/     Implémentations d'interfaces (Core ou autres packages)
│   ├── {Categorie}/
│   └── Manager/{ManagerName}/
├── Events/              Événements émis par le package (extends AbstractEvent)
├── Views/               Vues du panel admin
├── Public/              Vues/ressources publiques fournies par le package
├── Type/                Enums / value objects
├── Exception/           Exceptions du package
├── Functions/           Helpers (optionnel — ex. Users)
├── Init/
│   ├── Permissions.php  Déclaration des permissions du package
│   ├── init.sql         Tables créées à l'installation
│   └── uninstall.sql    Nettoyage à la désinstallation
└── Lang/
    ├── fr.php
    └── en.php
```

Générer le squelette : `php cmw package-init` — ⚠️ **cassé aujourd'hui** (génère le format V1) :
partir d'une copie de `App/Package/Faq` en attendant. Voir [10-CLI.md](10-CLI.md).

## 3. `Package.php`

Implémente `CMW\Manager\Package\IPackageConfigV2` (namespace `CMW\Package\{Package}`).

```php
namespace CMW\Package\Faq;

use CMW\Manager\Package\IPackageConfigV2;
use CMW\Manager\Package\PackageMenuType;
use CMW\Manager\Package\PackageSubMenuType;

class Package implements IPackageConfigV2
{
    public function name(): string { return 'Faq'; }
    public function version(): string { return '1.2.0'; }      // version du package
    public function cmwVersion(): string { return 'beta-01'; } // version CMS ciblée
    public function authors(): array { return ['CraftMyWebsite']; }
    public function imageLink(): ?string { return null; }
    public function isGame(): bool { return false; }           // package lié à un jeu ?
    public function isCore(): bool { return false; }           // non supprimable ?
    public function compatiblesPackages(): array { return ['Core']; }
    public function requiredPackages(): array { return ['Core']; }
    public function menus(): ?array { … }                      // entrées du menu admin
    public function uninstall(): bool { … }                    // false = désinstallation interdite
}
```

### Menus du panel admin

```php
new PackageMenuType(
    icon: 'fas fa-gear',
    title: LangManager::translate('core.menu.setting.main'),
    url: null,                       // null si le menu ne sert que de conteneur
    permission: null,
    subMenus: [
        new PackageSubMenuType(
            title: LangManager::translate('core.menu.setting.settings'),
            permission: 'core.settings.website',
            url: 'configuration',    // relatif au scope /cmw-admin/
            subMenus: []
        ),
    ],
)
```

`IPackageConfig` (V1) existe encore pour rétrocompatibilité, mais son **support est coupé pour la v1**.
Seul **NewsPro** l'utilise encore. Utiliser **V2** partout.

## 4. Contrôleurs

```php
namespace CMW\Controller\Faq;

use CMW\Manager\Package\AbstractController;
use CMW\Manager\Router\Link;
use CMW\Manager\Views\View;

class FaqController extends AbstractController
{
    #[Link('/faq', Link::GET, [], '/cmw-admin')]
    private function adminFaq(): void
    {
        UsersController::redirectIfNotHavePermissions('faq.show');

        View::createAdminView('Faq', 'main')
            ->addVariableList(['questions' => FaqModel::getInstance()->getAll()])
            ->view();
    }
}
```

## 5. Modèles & entités

```php
namespace CMW\Model\Faq;
class FaqModel extends AbstractModel { … }   // singleton via getInstance()

namespace CMW\Entity\Faq;
class FaqEntity extends AbstractEntity { … } // propriétés typées + constructor promotion
```

## 6. Permissions

Déclarées dans `Init/Permissions.php`, namespace `CMW\Permissions\{Package}`.
Format : `{package}.{feature}.{action}` — ex. `users.manage.edit`, `core.settings.website`, `faq.show`.

Vérification : `UsersController::hasPermission('faq.show')`.

## 7. Traductions

`Lang/fr.php` et `Lang/en.php` retournent un tableau associatif (imbriqué).
Appel : `LangManager::translate('faq.menu.title')` — le premier segment est le nom du package en minuscules.

**Aucun texte visible par l'utilisateur ne doit être écrit en dur.**

## 8. Interfaces & implémentations

Un package peut :

- **exposer** une interface dans `Interfaces/` (namespace `CMW\Interface\{Package}`) pour que d'autres
  packages s'y greffent ;
- **implémenter** une interface d'un autre package (ou du Core) dans
  `Implementations/{Categorie}/` (namespace `CMW\Implementation\{Package}\{Categorie}`).

La sélection se fait par poids — voir [02-Core-Architecture.md § 3](02-Core-Architecture.md).

## 9. Inventaire des packages présents en local

| Package | Version | CMS ciblé | Requis | Jeu | Core | Dépôt |
|---|---|---|---|---|---|---|
| **Core** | 1.0.0 | 2.0 | — | non | **oui** | *intégré* |
| **Users** | — | — | Core | non | **oui** | *intégré* |
| **Pages** | — | — | Core | non | **oui** | *intégré* |
| Faq | 1.2.0 | beta-01 | Core | non | non | `CraftMyWebsite/package-faq` |
| Media | 0.0.1 | beta-03 | — | non | non | *(dépôt privé)* |
| Minecraft | 1.2.1 | beta-01 | Core | **oui** | non | `CraftMyWebsite/package-minecraft` |
| News | 1.6.0 | beta-01 | Core | non | non | `CraftMyWebsite/package-news` |
| Rcon | 0.0.1 | beta-02 | Core | **oui** | non | *(dépôt privé)* |
| Redirect | 1.2.0 | beta-01 | Core | non | non | `CraftMyWebsite/package-redirect` |
| Votes | 1.2.0 | beta-01 | Core | non | non | `CraftMyWebsite/package-votes` |
| Wiki | 1.1.0 | beta-01 | Core | non | non | `CraftMyWebsite/package-wiki` |
| Litebans | 1.0.0 | beta-01 | Core, Users | non | non | `Overheat-Studio-Community/cmw-package-litebans` |
| NewsPro | 1.0.0 | beta-01 | Core, Users, OverApi, OverTranslations | non | non | *(dépôt privé, premium)* |
| OverApi | 1.4.0 | beta-01 | Core | non | non | `…/cmw-package-overapi` |
| OverEnv | 1.1.0 | beta-01 | Core, Users | non | non | `…/cmw-package-OverEnv` |
| OverTranslations | 0.0.1 | beta-01 | Core | non | non | *(dépôt privé, payant)* |
| SimpleCookies | 1.1.0 | beta-01 | Core | non | non | `…/cmw-package-simple-cookies` |
| SitemapExplorer | 1.0.0 | beta-02 | Core, OverApi | non | non | `…/cmw-package-sitemap_explorer` |

> **Versionnage CMS** : les packages ciblent des versions de type `alpha-XX` / `beta-XX`
> (le Core déclare `cmwVersion() === '2.0'`). Aujourd'hui c'est `.cmw-version` qui fait foi à
> l'installation d'une version ; **la cible est de tout faire reposer sur `App/Package/Core/Package.php`**.
> Le passage en v1 impliquera d'unifier le schéma et de mettre à jour `cmwVersion()` sur **tous** les
> dépôts — voir [08-Roadmap-V1.md § A](08-Roadmap-V1.md).
>
> **Périmètre v1** : tous les packages **officiels** (org `CraftMyWebsite`) doivent être v1-ready.
> Les packages Overheat Studio ne sont pas officiels — ils restent distribués sur le Marketplace mais
> ne bloquent pas la sortie.
