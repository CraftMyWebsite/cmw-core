# 11 — Extension JetBrains

> Plugin IDE officiel CMW pour PhpStorm / IntelliJ IDEA.
> Dépôt : https://github.com/CraftMyWebsite/extension-jetbrains
> Marketplace : https://plugins.jetbrains.com/plugin/26003-craftmywebsite
> Doc publique : https://craftmywebsite.fr/docs/fr/technical/outils/extension-jetbrains

---

## 1. Identité

| Info | Valeur |
|---|---|
| Nom / ID | `CraftMyWebsite` — `fr.craftmywebsite.extension` |
| Version | **1.0.3** |
| Langage | **Kotlin** (~67 ko) |
| Build | Gradle 8.4 + IntelliJ Platform Gradle Plugin |
| Plateforme cible | `IU` 2024.3.1.1 — `pluginSinceBuild = 243.22562`, pas de borne haute |
| Dépendances plugin | `com.jetbrains.php`, `com.intellij.modules.platform` |
| Marketplace | Publié, gratuit, ~144 téléchargements, note 4,41 |
| Licence | ❌ **aucune** (`licenseInfo: null`) |
| Dernier push | mars 2025 |

---

## 2. Ce que fait l'extension

Elle ajoute un groupe **CraftMyWebsite** dans le menu `New` de l'IDE (clic droit sur un dossier),
avec 12 actions :

### Création de composants d'un package

| Action | Génère |
|---|---|
| Create Controller | `{Feature}Controller.php` |
| Create Model | `{Feature}Model.php` |
| Create Entity | `{Feature}Entity.php` |
| Create Admin View | `*.admin.view.php` |
| Create Event | `{Feature}Event.php` |
| Create Type | `{Feature}.php` dans `Type/` |
| Create Interface | `I{Feature}.php` |
| Create Exception | `{Feature}Exception.php` |
| Create Implementation | Implémentation basée sur une interface existante |

### Création de modules complets

| Action | Génère |
|---|---|
| **Create Package** | Arborescence complète d'un package |
| **Create Theme** | Arborescence complète d'un thème |
| **Create Manager** | Un manager du Core (`App/Manager/`) |

---

## 3. Architecture du plugin

```
src/main/kotlin/fr/craftmywebsite/
├── actions/dialogs/
│   ├── packages/
│   │   ├── creation/          PackageAction, PackageDialogWrapper, *Model
│   │   └── files/             controller, model, entity, admin_view, event,
│   │                          type, interfaces, exception, implementation, publics
│   ├── themes/creation/       ThemeAction, ThemeDialogWrapper, ThemeCreationModel
│   └── managers/creation/     Manager, ManagerDialog
├── extensions/PhpTools.kt     Utilitaires PSI PHP
├── types/PackageTypes.kt
└── utils/                     Directory, Files, Managers, Packages, Templates, Themes

src/main/resources/
├── META-INF/plugin.xml        Déclaration des actions
└── templates/                 Templates .php.template (voir § 4)
```

Chaque action suit le même schéma : une `*Dialog`/`*Action` (formulaire IntelliJ) → un modèle de
données → `Templates` qui lit un `.template` des ressources et écrit le fichier via `Files.createFile()`.

---

## 4. Système de templates

`Templates.kt` est volontairement minimal :

```kotlin
fun createTemplateFile(templatePath: String, directory: PsiDirectory) {
    val data = this::class.java.getResourceAsStream(templatePath)
        ?.bufferedReader()?.use { it.readText() }
        ?: throw IllegalArgumentException("Template file not found at $templatePath")
    Files.createFile(directory, pathToFileName(templatePath), data)
}
```

- Le nom du fichier produit = nom du template **moins le suffixe `.template`**.
- Les variables sont des placeholders `${...}` (`${packageName}`, `${packageVersion}`,
  `${packageAuthor}`, `${themeName}`, `${themeCompatiblesPackages}`…), substitués côté Kotlin.

### Templates disponibles

**Packages** — `templates/package/files/`
`package.php`, `controller.php`, `model.php`, `entity.php`, `event.php`, `exception.php`,
`interface.php`, `implementation.php`, `type.php`, `lang.php`, `view.admin.php`, `public.view.php`,
`init/init.sql`, `init/permissions.php`, `init/uninstall.sql`

**Thèmes** — `templates/themes/files/`
`theme.php`, `router.php`, `config/config.php`, `config/config.settings.php`,
`assets/css/bootstrap.css`, `views/template.php`, `views/includes/{head,header,footer}.inc.php`,
`views/core/{home,cgu,cgv,maintenance}.view.php`, `views/core/alerts/{success,error,warning,info}.view.php`,
`views/core/router/demo.view.php`, `views/errors/{404,default}.view.php`

**Managers** — `templates/managers/files/manager.php`

### Ajouter un template

1. déposer le `.template` dans `src/main/resources/templates/…` ;
2. l'appeler via `Templates.createTemplateFile("/templates/…", directory)` ;
3. si une nouvelle action est nécessaire, la déclarer dans `META-INF/plugin.xml`.

---

## 5. État réel — points à corriger

> Constats au 15/08/2026, à partir du code du dépôt.

### 5.1 ⚠️ Génère les interfaces V1

C'est le point le plus important. Contrairement au CLI (qui produit un `infos.json` obsolète),
l'extension génère bien `Package.php` et `Theme.php` — **mais avec les interfaces V1** :

```php
// templates/package/files/package.php.template
use CMW\Manager\Package\IPackageConfig;
class Package implements IPackageConfig { … }
```

```php
// templates/themes/files/theme.php.template
use CMW\Manager\Theme\IThemeConfig;
class Theme implements IThemeConfig { … }
```

Or le **support V1 est coupé pour la v1** ([08-Roadmap-V1.md § B](08-Roadmap-V1.md)).
Les deux templates doivent passer en **`IPackageConfigV2` / `IThemeConfigV2`** — sinon l'extension
produira du code mort dès la sortie de la v1.

Détails associés :
- `theme.php.template` déclare `cmwVersion()` en dur à `"2.0"` avec un `//TODO get latest CMW version` ;
- il expose à la fois `author(): ?string` (V1) et `authors(): array` (V2, retournant un tableau vide) ;
- une accolade y est mal indentée (`imageLink()`).

### 5.2 Thèmes générés en Bootstrap

`templates/themes/files/assets/css/bootstrap.css.template` : les thèmes générés partent sur
**Bootstrap**, alors que la cible est **TailwindCSS 4** ([04-Themes.md](04-Themes.md)).

### 5.3 Support WebStorm bloqué par une dépendance

L'issue [#1](https://github.com/CraftMyWebsite/extension-jetbrains/issues/1) (*Add Webstorm support*,
assignée à @Teyir) se heurte à `plugin.xml` :

```xml
<depends>com.jetbrains.php</depends>
```

`com.jetbrains.php` n'existe pas dans WebStorm. Tant que cette dépendance est **obligatoire**,
le plugin ne peut pas s'y installer. Il faudrait la rendre optionnelle
(`<depends optional="true" config-file="…">`) et isoler le code qui utilise le PSI PHP.

### 5.4 Autres

| Point | Détail |
|---|---|
| Issue [#5](https://github.com/CraftMyWebsite/extension-jetbrains/issues/5) | *Create Model* produit `Xxxx.php` au lieu de `XxxxModel.php` (les autres actions suffixent correctement) |
| Changelog | `change-notes` de `plugin.xml` s'arrête à **1.0.1** alors que la version est **1.0.3** |
| Licence | Aucune licence sur le dépôt, alors que le Core est en GNU GPL / CC BY-NC-ND |
| Plateforme | Ciblée sur 2024.3 ; à re-tester sur les IDE récents |
| Maintenance | Aucun commit depuis mars 2025 |

---

## 6. Extension vs CLI

Les deux outils font **le même travail**, avec des qualités inverses :

| Critère | Extension JetBrains | CLI (`php cmw`) |
|---|---|---|
| Format généré | `Package.php` / `Theme.php` ⚠️ **V1** | `infos.json` ❌ **format V1 obsolète** |
| Arborescence | ✅ complète (Interfaces, Events, Type, Exception, Public, Permissions, uninstall.sql) | ❌ partielle |
| Permissions | ✅ `init/permissions.php` | ❌ non généré |
| Granularité | ✅ 12 actions, création fichier par fichier | ❌ 2 générateurs monolithiques |
| CSS des thèmes | ⚠️ Bootstrap | ⚠️ extensions demandées puis ignorées |
| Portabilité | PhpStorm / IntelliJ uniquement | Multi-plateforme (sauf `ai-copilot`, Unix only) |

> **À retenir pour la réécriture du CLI** ([10-CLI.md § 7](10-CLI.md)) : les templates de l'extension
> constituent une base saine et bien plus proche de la cible. Plutôt que de repartir de zéro,
> les deux outils gagneraient à **partager le même jeu de templates** — une fois ceux-ci migrés en V2.

---

## 7. Chantiers

| # | Chantier | Priorité |
|---|---|---|
| 1 | Migrer `package.php.template` et `theme.php.template` en **V2** | ⬛ haute |
| 2 | Corriger le nom de fichier des Models (issue #5) | ⬛ haute |
| 3 | Passer les thèmes générés en **TailwindCSS 4** | ⬜ moyenne |
| 4 | Rendre `com.jetbrains.php` optionnelle pour débloquer WebStorm (issue #1) | ⬜ moyenne |
| 5 | Résoudre `cmwVersion()` dynamiquement au lieu du `"2.0"` en dur | ⬜ moyenne |
| 6 | Mettre le changelog à jour et ajouter une licence | ⬜ basse |
| 7 | Mutualiser les templates avec le CLI | ⬜ basse |
| 8 | Re-tester / relever la compatibilité IDE | ⬜ basse |

> ⚠️ Le dépôt n'est **pas cloné en local** — travailler dessus suppose de le cloner à part
> (ce n'est ni un package ni un thème du CMS).
