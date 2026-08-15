# 05 — UI du panel d'administration (Dashboard)

## 1. Où est quoi

Le panel admin est réparti sur deux emplacements :

| Emplacement | Contenu |
|---|---|
| `Admin/` | Ressources globales du dash : config Tailwind, CSS compilé, vendors, vues communes |
| `Public/Themes/Dashboard/` | **Thème du dash** — showcase de tous les composants UI, dépôt privé de l'org `CraftMyWebsitePro` |
| `App/Package/{X}/Views/` | Vues admin de chaque package, rendues via `View::createAdminView()` |

**Showcase en ligne : https://dash.craftmywebsite.fr/** — c'est la référence visuelle vivante.
Le dépôt local `Public/Themes/Dashboard` en est le miroir : chaque composant y a une page de démo
avec son code affiché (highlight.js).

## 2. Stack

- **TailwindCSS 3** en `darkMode: "class"` — ⚠️ **la cible est TailwindCSS 4**, migration à prévoir
  (voir [04-Themes.md § 7](04-Themes.md) et [08-Roadmap-V1.md](08-Roadmap-V1.md))
- **Flowbite 1.6** (plugin Tailwind + JS)
- **ApexCharts** pour les graphiques
- **FontAwesome** pour les icônes
- Toutes les couleurs passent par des **variables CSS** (`var(--light-primary)`, `var(--dark-primary)`…)
  → thème clair/sombre et recoloration sans recompiler.

### Compilation

```bash
npm run tw-core
# npx tailwindcss -i Admin/Tailwind/tailwindInput.css \
#                 -o Admin/Resources/Assets/Css/style.css \
#                 --config Admin/Tailwind/tailwind-dashboard.config.js --watch --minify
```

`content` scanné : `Admin/**`, `App/Package/**/Views/**`, `node_modules/flowbite/**`.

> ⚠️ Les vues admin d'un package **doivent** être dans `App/Package/{X}/Views/` pour que Tailwind
> détecte leurs classes. Sinon les styles seront purgés à la compilation.

`Admin/Tailwind/cssgenerator.html` : outil de génération/prévisualisation des palettes de couleurs.

## 3. Catalogue des composants

Routes du showcase (`Public/Themes/Dashboard/router.php`) et fichiers correspondants :

### Layouts — `Views/Layouts/`
| Route | Fichier |
|---|---|
| `/layouts/grid` | `grid.view.php` |
| `/layouts/grid-advanced` | `advanced.view.php` |
| `/layouts/flex` | `flex.view.php` |
| `/layouts/loader` | `loaderPage.view.php` |

### Formulaires — `Views/Forms/`
| Route | Fichier |
|---|---|
| `/forms/input` | `input.view.php` |
| `/forms/button` | `button.view.php` |
| `/forms/select` | `select.view.php` |
| `/forms/toggle` | `toggle.view.php` |
| `/forms/textarea` | `textarea.view.php` |
| `/forms/imgDropper` | `imgDropper.view.php` — dropzone image (multi-images supporté) |
| `/forms/fileDropper` | `fileDropper.view.php` |
| `/forms/fapicker` | `fapicker.view.php` — sélecteur d'icône FontAwesome |

### Composants — `Views/Components/`
`accordion`, `alert`, `apexChart` (route `/components/chart`), `avatar`, `badge`, `carousel`,
`class`, `dropdown`, `kbd`, `modal`, `pagination`, `tabs`, `text`, `tooltip`.

### Tableaux — `Views/Tables/`
`default`, `sorted`, `selectable`.

### Autres
`Views/Alerts/` (error, success, warning), `Views/Core/home`, `Views/Users/*`
(login, register, profile, 2fa, enforce2fa, forgot_password), `Views/Errors/` (404, default),
`Views/Includes/` (head, header, footer), `Views/template.php`.

## 4. Classes utilitaires maison

Définies dans `Admin/Tailwind/tailwindInput.css` via `@apply`. Extrait des plus courantes :

| Classe | Usage |
|---|---|
| `.main-content` | Conteneur principal (gère l'offset sidebar/navbar) |
| `.card` / `.card-title` | Carte de contenu |
| `.page-title` | Titre de page (`<section class="page-title"><h4>…`) |
| `.nav`, `.aside-nav`, `.a-side-nav`, `.side-nav-active`, `.a-side-nav-dropdown`, `.span-side-nav` | Navigation |
| `.sidebar-collapsed` | État replié de la sidebar |
| `.btn-primary`, `.btn-info`, `.btn-success`, `.btn-warning`, `.btn-danger` | Boutons |
| suffixes `-sm` / `-xl` | Variantes de taille (`.btn-primary-sm`, `.btn-danger-xl`) |
| `.loading-btn` + `data-loading-btn="Loading..."` | Bouton avec état de chargement automatique |
| `.loading-icon` | Spinner |
| `.link` | Lien texte |

Exemple type d'une vue admin :

```php
<?php use CMW\Utils\Website;
Website::setTitle('Ma page');
Website::setDescription('…');
?>
<section class="page-title">
    <h4><i class="fa-solid fa-stop"></i> Ma page</h4>
</section>

<div class="card">
    <h6 class="card-title">Section</h6>
    <button type="button" class="btn-primary loading-btn" data-loading-btn="Chargement...">
        Valider
    </button>
</div>
```

## 5. Composants côté PHP — `App/Manager/Components/`

En parallèle du HTML, le Core expose un `ComponentsManager` et une base de composants PHP
(`App/Manager/Components/Base/`) : `AComponentBase`, `ButtonComponentBase`, `DivComponentBase`,
`FormComponentBase`, `HeaderComponentBase`, `HeadingComponentBase`, `HrComponentBase`,
`InputComponentBase`, `LabelComponentBase`, `SelectComponentBase`, `SelectOptionComponentBase`,
`TextareaComponentBase`, plus l'interface `IComponent`.

> ⚠️ **C'est un POC.** Ce système n'a **rien à voir** avec les composants HTML/Tailwind du thème
> Dashboard — les deux ne sont pas en concurrence et ne couvrent pas le même besoin.
> Ne pas s'en servir comme référence pour construire l'UI du panel admin.

## 6. Règle de travail

Avant de créer un nouveau composant admin : **vérifier d'abord s'il existe déjà** dans
`Public/Themes/Dashboard/Views/` ou sur https://dash.craftmywebsite.fr/.
Le dash doit rester visuellement homogène — pas de style ad-hoc dans les vues de packages.
