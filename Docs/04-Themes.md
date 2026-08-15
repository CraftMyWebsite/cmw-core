# 04 — Thèmes

## 1. Principe

Un thème définit **tout le rendu public** du site. Il vit dans `Public/Themes/{Nom}/` et fournit les vues
de chaque package qu'il supporte. Un thème peut donc redéfinir l'affichage de n'importe quel package.

TailwindCSS n'est **pas obligatoire** pour un thème, mais **tous les thèmes officiels CMW l'utilisent**.

> ### ⚠️ TailwindCSS 4 obligatoire pour tout nouveau thème
>
> **Tout nouveau thème doit être écrit en TailwindCSS 4.**
>
> État actuel du dépôt : Core et thème `Sampler` déclarent `"tailwindcss": "^3.2.2"`
> (version réellement installée : **3.4.18**). La migration v3 → v4 de l'existant est un
> chantier identifié — voir [08-Roadmap-V1.md](08-Roadmap-V1.md).
>
> Rappels sur les différences structurantes de la v4 :
> - configuration **CSS-first** : `@import "tailwindcss";` + `@theme { … }` dans le fichier d'entrée,
>   à la place du `tailwind.config.js` (qui reste chargeable via `@config` pour la transition) ;
> - détection automatique des sources — plus besoin de déclarer `content:` ;
> - CLI déplacée dans le paquet **`@tailwindcss/cli`** : la commande devient
>   `npx @tailwindcss/cli -i … -o … --watch --minify` ;
> - **Flowbite** doit être passé en version compatible v4 (`@plugin "flowbite/plugin"`).

| Thème | Rôle |
|---|---|
| **Sampler** | Thème public **par défaut**, livré dans le Core |
| **Wipe** | Thème public de **référence / exemple** (repo `CraftMyWebsite/theme-wipe`) |
| **Dashboard** | Thème du **panel admin** — voir [05-Dashboard-UI.md](05-Dashboard-UI.md) |

Générer un squelette : `php cmw theme-init` — ⚠️ **cassé aujourd'hui** (génère le format V1) :
partir d'une copie de `Public/Themes/Sampler` en attendant. Voir [10-CLI.md](10-CLI.md).
Archive d'exemple officielle : https://github.com/CraftMyWebsite/cmw-doc/raw/main/Assets/Zip/Example-Theme-V2.zip

## 2. Anatomie

```
Public/Themes/{Nom}/
├── Theme.php                   ★ métadonnées (implémente IThemeConfigV2)
├── router.php                  Routes custom du thème
├── package.json                Script de compilation Tailwind du thème
├── Assets/
│   ├── Css/                    CSS compilé (style.css)
│   ├── Js/
│   └── Webfonts/
├── Resources/
│   ├── input.css               Entrée Tailwind
│   ├── tailwind.config.js
│   └── default.png             Aperçu du thème (imageLink)
├── Config/
│   ├── config.settings.php     ★ définition du configurateur (EditorMenu / EditorValue)
│   └── Default/                Ressources par défaut (images…)
├── Components/                 Composants réutilisables du thème (optionnel)
└── Views/
    ├── template.php            ★ gestionnaire de rendu du thème
    ├── Includes/               head.inc.php, header.inc.php, footer.inc.php
    ├── Core/                   home, cgu, cgv, maintenance + Alerts/
    ├── Errors/                 404.view.php, default.view.php
    ├── Users/                  login, register, profile, 2fa, enforce2fa, forgot_password
    ├── Pages/                  main.view.php
    ├── CustomRoutes/           Vues des routes déclarées dans router.php
    └── {Package}/              Vues des packages supportés (News, Faq, Wiki…)
```

### Règles de nommage

- **Dossiers** : première lettre en majuscule, le reste en minuscules.
- **Fichiers** : tout en minuscules — sauf `Theme.php`.
- Les vues portent l'extension `.view.php`, les includes `.inc.php`.

## 3. `Theme.php`

```php
namespace CMW\Theme\Sampler;

use CMW\Manager\Env\EnvManager;
use CMW\Manager\Theme\IThemeConfigV2;

class Theme implements IThemeConfigV2
{
    public function name(): string { return 'Sampler'; }
    public function version(): string { return '0.0.2'; }
    public function cmwVersion(): string { return 'alpha-09'; }
    public function authors(): array { return ['CraftMyWebsite']; }
    public function compatiblesPackages(): array { return ['Core', 'Pages', 'Users']; }
    public function requiredPackages(): array { return ['Core', 'Users']; }
    public function imageLink(): ?string
    {
        return EnvManager::getInstance()->getValue('PATH_SUBFOLDER')
             . 'Public/Themes/Sampler/Resources/default.png';
    }
}
```

`IThemeConfig` (V1) est encore supporté via `LegacyThemeAdapter`, mais son **support est coupé pour la
v1** — plus aucun thème local ne l'utilise. Utiliser **V2** partout.

## 4. `router.php`

Permet d'ajouter des routes propres au thème :

```php
use CMW\Manager\Loader\Loader;

// Route "/hello" servie par Views/CustomRoutes/hello.view.php
Loader::createSimpleRoute('/hello', 'hello', 'CustomRoutes');
```

## 5. Configurateur de thème (live builder) ★

Le point fort de CMW : l'utilisateur final personnalise le thème depuis le panel admin, en live.

### 5.1 Déclaration — `Config/config.settings.php`

Retourne un tableau d'`EditorMenu` :

```php
use CMW\Manager\Theme\Editor\Entities\{EditorMenu, EditorValue, EditorType,
                                       EditorSelectOptions, EditorRangeOptions};

return [
    new EditorMenu(
        title: 'Globaux',
        key: 'global',            // ← le "X" des data-cmw
        scope: null,              // limite le menu à une page/scope
        requiredPackage: null,    // menu affiché seulement si le package est installé
        values: [
            new EditorValue(
                title: 'Couleur principale',
                themeKey: 'main_color',   // ← le "Y" des data-cmw
                defaultValue: '#FB2388',
                type: EditorType::COLOR,
            ),
            new EditorValue(
                title: 'Police d\'écriture',
                themeKey: 'main_font',
                defaultValue: 'font-exo2',
                type: EditorType::SELECT,
                selectOptions: [
                    new EditorSelectOptions(value: 'font-exo2', text: 'exo2'),
                    // …
                ],
            ),
        ],
    ),
];
```

### 5.2 Types disponibles — `EditorType`

`COLOR`, `TEXT`, `NUMBER`, `IMAGE`, `CSS`, `TEXTAREA`, `BOOLEAN`, `SELECT`, `RANGE`,
`FONTAWESOMEPICKER` (`faPicker`), `HTML`.

### 5.3 Utilisation dans les vues — attributs `data-cmw`

La clé est toujours `"{menuKey}:{themeKey}"` (le `X:Y` de la doc).

| Attribut | Effet | Types compatibles |
|---|---|---|
| `data-cmw="X:Y"` | Injecte du texte / HTML dans l'élément | TEXT, TEXTAREA, HTML, NUMBER, SELECT |
| `data-cmw-attr="attr:X:Y"` | Définit un attribut HTML (plusieurs séparés par un **espace**) | TEXT, IMAGE, SELECT, (CSS déconseillé) |
| `data-cmw-style="prop:X:Y"` | Applique un style CSS (plusieurs séparés par `;`) | TEXT, COLOR, IMAGE, SELECT, RANGE |
| `data-cmw-class="X:Y"` | Applique une ou plusieurs classes (séparées par un **espace**) | TEXT, TEXTAREA, FONTAWESOMEPICKER, SELECT, RANGE |
| `data-cmw-visible="X:Y"` | Affiche/masque l'élément (et son contenu) | BOOLEAN |
| `data-cmw-var="--var:X:Y"` | Définit une variable CSS custom (plusieurs séparées par un **espace**) | COLOR, TEXT, SELECT |

Exemples :

```html
<p data-cmw="global:site_title"></p>

<img data-cmw-attr="src:hero:image alt:hero:alt">

<p data-cmw-style="color:global:main_color;font-size:global:font_size"></p>

<i data-cmw-class="hero:icon" class="text-7xl"></i>

<div data-cmw-visible="hero:show_block">
  <p>Élément 1</p>
</div>

<div style="background: linear-gradient(to bottom, var(--main-color), var(--bg-color));"
     data-cmw-var="--main-color:global:main_color --bg-color:global:bg_color"></div>
```

**Combinaisons** : plusieurs `data-cmw-*` différents sur une même balise, c'est valide :

```html
<a data-cmw-visible="X:Y" data-cmw-style="color:X:Y" data-cmw-class="X:Y"
   data-cmw-attr="href:X:Y" data-cmw="X:Y"></a>
```

⛔ **Interdit** : deux fois le *même* attribut sur une balise
(`data-cmw-style="color:X:Y"` **et** `data-cmw-style="background:X:Y"` → ne fonctionne pas ;
il faut les cumuler dans un seul attribut séparés par `;`).

### 5.4 Lecture en PHP

```php
ThemeModel::getInstance()->fetchConfigValue('global', 'main_color');
```

⚠️ Cet appel **n'est pas suivi par le live builder** : la valeur ne se met pas à jour en direct dans
l'aperçu. Préférer les `data-cmw` dès que possible.

## 6. Manager de thèmes — `App/Manager/Theme/`

| Fichier | Rôle |
|---|---|
| `Loader/ThemeLoader.php` | Résolution et chargement du thème actif (`getCurrentTheme()`) |
| `ThemeManager.php` | Gestion générale |
| `Config/ThemeConfigResolver.php`, `ThemeMapper.php`, `ThemeSettingsMapper.php` | Résolution/mapping de la config |
| `Editor/ThemeEditorProcessor.php` | Traitement du live builder (parsing des `data-cmw`) |
| `Editor/Entities/*` | `EditorMenu`, `EditorValue`, `EditorType`, `EditorSelectOptions`, `EditorRangeOptions` |
| `File/ThemeFileManager.php` | Manipulation des fichiers de thème |
| `Market/ThemeMarketManager.php` | Market des thèmes |
| `Adapter/LegacyThemeAdapter.php` | Compatibilité `IThemeConfig` V1 |
| `IThemeConfig.php` / `IThemeConfigV2.php` | Contrats |
| `Exceptions/ThemeNotFoundException.php`, `UninstallThemeType.php` | Divers |

## 7. Compilation TailwindCSS d'un thème

Chaque thème embarque son `package.json`.

### Cible — TailwindCSS 4

```jsonc
// package.json du thème
{
  "scripts": {
    "MonTheme": "npx @tailwindcss/cli -i ./Resources/input.css -o ./Assets/Css/style.css --watch --minify"
  },
  "devDependencies": { "@tailwindcss/cli": "^4.0.0", "tailwindcss": "^4.0.0" }
}
```

```css
/* Resources/input.css */
@import "tailwindcss";

@theme {
  --font-main: "Exo 2", sans-serif;
  --color-brand: #FB2388;
}
```

Plus de `--config` ni de bloc `content:` : la v4 se configure dans le CSS et détecte ses sources
automatiquement.

### Existant — TailwindCSS 3 (à migrer)

Sampler et le Core sont encore en v3 :

```bash
npm run Sampler   # depuis Public/Themes/Sampler/
# npx tailwindcss -i Resources/input.css -o Assets/Css/style.css \
#   --config Resources/tailwind.config.js --watch --minify

npm run tw-core   # panel admin, depuis la racine
```

| Cible | Version déclarée | Version installée |
|---|---|---|
| Core (`package.json`) | `^3.2.2` | 3.4.18 |
| Thème `Sampler` | `^3.2.2` | 3.4.18 |

Flowbite est en `^1.6.3` — une version compatible v4 sera nécessaire lors de la migration.
