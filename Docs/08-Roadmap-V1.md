# 08 — Roadmap vers la v1 stable

> Contexte : le projet a été laissé de côté un moment, tourne déjà en production sur plusieurs sites,
> et l'objectif est désormais de **sortir la première version stable**.
> Constats relevés dans le code au 14/08/2026 ; décisions actées le 14/08/2026.

---

## 0. Décisions actées ✅

| Sujet | Décision |
|---|---|
| **Source de vérité de la version** | Tout doit passer sur **`App/Package/Core/Package.php`**. Aujourd'hui c'est `.cmw-version` qui fait foi à l'installation d'une version → **à migrer**. |
| **Périmètre v1** | **Tous les packages officiels** doivent être v1-ready. Les packages Overheat Studio ne sont pas officiels (mais restent sur le Marketplace) → hors périmètre bloquant. |
| **Compatibilité ascendante** | **Non bloquante** — on est encore en beta, une réinstallation complète est acceptable. Des **migrations de données** seront proposées. |
| **E-commerce** | `CraftMyWebsite/package-shop` existe mais est **expérimental** → traité en **seconde partie**, hors v1 initiale. |
| **PHP** | Plancher **8.4**. |
| **Composer** | Le **Core doit l'éviter au maximum** (poids et dépendances). Un package tiers peut l'utiliser s'il le souhaite. Le modèle vendorisé est donc **assumé**. |
| **Tests** | Hors scope immédiat, souhaitables à terme. |
| **`App/Manager/Components/`** | C'était un **POC**. Sans rapport avec les composants du thème Dashboard — les deux ne sont pas en concurrence. |
| **Legacy V1** (`IPackageConfig`, `IThemeConfig`) | **Support coupé** pour la v1. |
| **`robots.txt` / `sitemap.xml`** | **Auto-générés à l'installation** → volontairement non versionnés. |
| **Documentation** | `cmw-doc` doit être mis à jour **à chaque modification**. `.copilot/Technical/` reste gitignoré ; un script de sync ou un lien vers `cmw-doc` reste à mettre en place pour `Docs/`. |
| **Ordre de travail** | 1) cartographier · 2) recenser tous les soucis · 3) traiter les issues GitHub · 4) définir la roadmap v1 détaillée. |

---

## A. Migration du versionnage ★

État actuel, incohérent :

| Source | Valeur | Statut cible |
|---|---|---|
| `.cmw-version` (généré, gitignoré) | — | ⚠️ fait foi aujourd'hui → **à retirer** |
| `App/Package/Core/Package.php` → `version()` | `1.0.0` | ✅ **source de vérité cible** |
| `App/Package/Core/Package.php` → `cmwVersion()` | `2.0` | à aligner |
| `EnvManager::getValue('VERSION')` | `DEV` si absente | consommateur, plus source |
| `README.md` | `v2.0.0-alpha` | à mettre à jour |
| `Public/Themes/Sampler/Theme.php` → `cmwVersion()` | `alpha-09` | à aligner |
| Packages externes → `cmwVersion()` | `beta-01`, `beta-02`, `beta-03` | à aligner |

**Travaux :**
1. Faire lire la version par `UpdatesManager::getVersion()` depuis `Core\Package::version()`
   au lieu de `.env VERSION` / `.cmw-version`.
2. Choisir le format de version v1 (SemVer `1.0.0` recommandé) et remplacer les `alpha-XX` / `beta-XX`.
3. Définir comment `cmwVersion()` des packages/thèmes est comparé à la version du CMS —
   aujourd'hui c'est une comparaison de chaînes, il faut un vrai comparateur de contraintes.
4. Répercuter sur **tous** les dépôts packages/thèmes.

---

## B. Suppression du legacy V1

- `IPackageConfig` (V1) : encore implémenté par **NewsPro** (Overheat) → prévenir avant de couper.
- `IThemeConfig` (V1) + `App/Manager/Theme/Adapter/LegacyThemeAdapter.php` : plus aucun thème local ne
  l'utilise → suppression possible immédiatement.
- Vérifier les thèmes/packages non clonés localement avant suppression définitive.

---

## C. Alignement PHP 8.4 — ✅ fait le 14/08/2026

| Constat | Action réalisée |
|---|---|
| CI en **PHP 8.1** | ✅ Matrice passée en **8.4** |
| Runner `windows-2019` | ✅ Passé en `windows-latest` (le runner 2019 a été retiré par GitHub) |
| Dockerfile en `php:8.3.12-apache` | ✅ Passé en `php:8.4-apache` |
| Dépréciations 8.4 dans le code | ✅ **9 corrigées** — paramètres implicitement nullable |
| Doc « PHP 8.3 ou supérieur » | ✅ **Fait** dans `cmw-doc` (5 fichiers FR + EN) le 15/08/2026 |

### Détail des dépréciations corrigées

Toutes du type *« Implicitly marking parameter `$x` as nullable is deprecated »* :

| Fichier | Méthodes |
|---|---|
| `App/Manager/Api/APIManager.php` | `generateHeader()`, `postRequest()`, `getRequest()`, `createResponse()` — paramètre `$cmwlToken` |
| `App/Package/Core/Models/ThemeModel.php` | `fetchConfigValue()`, `fetchImageLink()`, `fetchVideoLink()`, `getConfigValue()` |
| `App/Manager/Twofa/Vendors/twofactorauth/…/HttpTimeProvider.php` | `__construct()` — paramètre `$options` (lib vendorisée, patchée sur place) |

`string $x = null` → `?string $x = null` : la signature s'élargit, **aucun appel existant n'est cassé**.

Les thèmes (`Public/Themes/`) et tous les packages présents en local sont **exempts** de dépréciation.

> ⚠️ Vérification faite avec le PHP local, qui est en **8.5.7** (le formula Homebrew `php@8.4` pointe
> en réalité sur 8.5). Les dépréciations « implicitly nullable » sont apparues en 8.4 et subsistent en
> 8.5, donc le résultat est valide — mais c'est bien la CI en 8.4 qui fera foi.
> `php -l` ne détecte que les dépréciations émises à la compilation : celles de **runtime**
> (ex. passage de `null` à un paramètre non nullable d'une fonction interne) ne sont pas couvertes.

---

## D. Qualité & CI — ✅ partiellement fait le 14/08/2026

| Constat | État |
|---|---|
| CI inopérante (ne faisait que `setup-php`) | ✅ **Job `lint` bloquant** : syntaxe + dépréciations sur `App`, `Admin`, `Installation`, `Public`, `index.php`, `cmw` |
| Formatage non vérifié | ⚠️ **Job `format` non bloquant** (`continue-on-error: true`) |
| CI déclenchée seulement sur `main` | ✅ Étendue à `dev` (la branche de développement réelle) |
| Tests | ⬜ Aucun — hors scope immédiat, souhaité à terme |

### Dette de formatage

`pretty-php --check` signale **177 fichiers non conformes sur 319** (au 14/08/2026), principalement :
- *Braces not used in T_IF control structure* (`if` sans accolades)
- *Empty statement*

D'où le `continue-on-error: true` : rendre le job bloquant tout de suite ferait échouer toutes les PR.

**Chantier à part :** passer `php pretty-php.phar *` sur l'ensemble du dépôt en **un commit dédié**
(à isoler pour ne pas polluer l'historique), puis retirer `continue-on-error`.

> Note : `pretty-php.phar` exige `register_argc_argv=1` — sans quoi il lève une
> `InvalidRuntimeConfigurationException`. C'est configuré dans le workflow ;
> en local il faut lancer `php -d register_argc_argv=1 pretty-php.phar …` si le php.ini ne l'active pas.

---

## E. Performance

| Constat | Emplacement |
|---|---|
| Appel API à chaque rendu de menu admin | `Core\Package::menus()` appelle `UpdatesManager::checkNewUpdateAvailable()` → `PublicAPI::getData('cms/latest')` **à chaque affichage de page admin** |
| TODO explicite | `UpdatesManager::getCmwLatest()` porte un `@todo Cache this data` |
| Scan disque répété | `Loader::loadImplementations()` fait un `scandir` complet de tous les packages **à chaque appel**, sans mise en cache |

`SimpleCacheManager` existe déjà — candidat naturel pour ces deux points.

---

## F. Dettes techniques

### F.1 `getHighestImplementation()`

```php
$index = 0;
$highestWeight = 1;
foreach ($implementations as $implementation) {
    if ($implementation->weight() > $highestWeight) { … }
}
```

Le poids initial est `1` : si toutes les implémentations ont un poids ≤ 1, c'est **la première trouvée
dans l'ordre de scan disque** qui gagne — comportement implicite et non déterministe entre machines.

### F.2 `Loader::setLocale()`

```php
EnvManager::getInstance()->addValue('locale', 'fr');  // Why fr ?
```

Le commentaire est dans le code. La locale est forcée alors que `.env` expose une variable `LOCALE`.
Impacte tout l'i18n.

### F.3 Sécurité des libs vendorisées

Composer étant écarté par choix, les libs sont copiées dans le dépôt :
`App/Manager/Mail/Vendors/Phpmailer/`, `App/Manager/Twofa/Vendors/`, `App/Package/Minecraft/Vendors/`.

**Conséquence assumée mais à outiller** : il faut un moyen de suivre les CVE de ces libs
(a minima noter la version embarquée + la date de la dernière mise à jour dans un fichier dédié).

> PHPMailer + `MailManager` sont actuellement **modifiés et non committés** dans le working tree —
> à qualifier avant de committer.

---

## F bis. Outillage développeur obsolète ★

> **Constat validé le 15/08/2026 : le CLI et l'extension JetBrains sont tous deux
> très en retard sur le CMS.** À traiter comme **un seul chantier « outillage »**, pas comme deux
> corrections isolées — les deux outils font le même travail et doivent converger.

Ce sont les **deux portes d'entrée des créateurs de packages et de thèmes**. Tant qu'ils produisent du
code mort, chaque nouveau contributeur démarre sur une base cassée. C'est un chantier à faible coût
technique mais à fort impact sur l'adoption.

### État des deux outils

| | CLI (`php cmw`) | Extension JetBrains |
|---|---|---|
| Format généré | ❌ `infos.json` (format V1 abandonné) | ⚠️ `Package.php` / `Theme.php` mais en **interfaces V1** |
| Arborescence | ❌ partielle | ✅ complète |
| Permissions | ❌ non générées | ✅ générées |
| CSS des thèmes | ⚠️ extensions ignorées | ⚠️ Bootstrap |
| Dernière évolution | — | mars 2025 |

**Cible commune** : interfaces **V2**, arborescence conforme à [03](03-Packages.md)/[04](04-Themes.md),
thèmes en **TailwindCSS 4**, et à terme **un jeu de templates unique partagé par les deux outils**.

### Détail — CLI

Les deux générateurs produisent du **code au format V1 obsolète** et **inutilisable en l'état** —
constaté par exécution le 15/08/2026 :

| Commande | Problème principal |
|---|---|
| `php cmw theme-init` | Génère `infos.json` au lieu de `Theme.php` (V2) ; casse des dossiers incohérente (`views/` vs `Views/` — bug silencieux sur macOS, **deux dossiers sur Linux**) ; ni `router.php`, ni `Config/`, ni `Resources/` ; extensions demandées puis ignorées |
| `php cmw package-init` | Génère `infos.json` au lieu de `Package.php` (V2) ; l'implémentation générée n'est **jamais chargée** (mauvais emplacement + mauvais namespace) ; `Init/Permissions.php` non généré alors que le contrôleur généré exige une permission ; vue en **Bootstrap** |
| `php cmw ai-copilot` | ✅ fonctionne, mais **Unix uniquement** (`rm -rf` / `cp -r` via `shell_exec`) et ne récupère que `FR/Technical` |

Autre point : `App/Bootstrap/standalone.php` n'est **inclus par personne** — code mort.

Analyse détaillée et liste des chantiers : [10-CLI.md](10-CLI.md).

### Détail — extension JetBrains

Le plugin IDE `CraftMyWebsite` ([11-Extension-JetBrains.md](11-Extension-JetBrains.md)) fait le même
travail **en mieux** : arborescence complète, `Permissions.php`, `uninstall.sql`, 12 actions de
génération. Mais ses templates ciblent encore **`IPackageConfig` / `IThemeConfig` (V1)** — à migrer en
V2 en même temps que la coupe du legacy (§ B), sous peine de produire du code mort dès la v1.
S'y ajoutent : thèmes générés en Bootstrap, WebStorm bloqué par `<depends>com.jetbrains.php</depends>`,
et aucune évolution depuis mars 2025.

### Ordre de traitement suggéré

1. **Migrer les templates de l'extension en V2** — c'est la base la plus saine, et elle sert déjà les
   contributeurs via le Marketplace.
2. **Réécrire les générateurs du CLI** en consommant ce même jeu de templates.
3. Passer les templates de thème en **TailwindCSS 4**.
4. Traiter les points spécifiques (WebStorm, issue #5, portabilité d'`ai-copilot`).

> ⚠️ Dépendance : la migration V2 des templates doit être **synchronisée avec la coupe du legacy V1**
> (§ B). Couper le support V1 sans migrer les outils rendrait tout scaffolding inutilisable du jour au
> lendemain.

---

## F ter. Migration TailwindCSS 3 → 4 ★

**Décision : tout nouveau thème doit être en TailwindCSS 4.** L'existant est encore en v3 et doit migrer.

| Cible | Déclaré | Installé |
|---|---|---|
| Core (`package.json`, panel admin) | `^3.2.2` | 3.4.18 |
| Thème `Sampler` | `^3.2.2` | 3.4.18 |
| Flowbite | `^1.6.3` | — version compatible v4 requise |

Points d'attention de la v4 :
- configuration **CSS-first** (`@import "tailwindcss"` + `@theme`) en remplacement de
  `tailwind.config.js` — impacte `Admin/Tailwind/tailwind-dashboard.config.js` et le fichier de
  config de chaque thème ;
- plus de bloc `content:` (détection automatique des sources) ;
- CLI déplacée dans `@tailwindcss/cli` → scripts `npm` à mettre à jour (`tw-core`, `Sampler`, …) ;
- les couleurs du dash passent déjà par des **variables CSS**, ce qui facilite le passage à `@theme` ;
- Flowbite doit être monté en version compatible v4.

À faire thème par thème, en commençant par le panel admin (`Admin/`) puis `Sampler`.

---

## G. Packages absents en local

Ces dépôts officiels ne sont pas clonés et devront l'être pour la v1 :

`package-calendar`, `package-contact`, `package-forum`, `package-newsletter`, `package-support`,
`package-shop` *(2ᵉ partie)*, ainsi que la majorité des thèmes
(`theme-nethercraft`, `theme-nightcraft`, `theme-pixcraft`, `theme-rainfall`, `theme-vega`).

---

## H. Documentation

| Constat | Action |
|---|---|
| `README.md` annonce **ALPHA** / `v2.0.0-alpha`, copyright arrêté à 2025 | Mettre à jour |
| Certaines pages de doc pointent vers `reborn.craftmywebsite.fr` | Corriger dans `cmw-doc` |
| `.copilot/Technical/` gitignoré, non versionné avec le Core | Script de sync vers `Docs/` **ou** lien vers `cmw-doc` — à trancher |
| `Docs/` (ce dossier) est neuf | À maintenir en phase avec le code |

---

## I. Divers

- Des `.DS_Store` traînent (`./`, `Admin/Resources/Assets/`) — à ajouter au `.gitignore`.
- `.ignored_packages` contient actuellement `News` (désactivation locale).

---

## J. Issues GitHub

**52 issues ouvertes** sur l'org `CraftMyWebsite`, dont 11 sur le Core.
Détail et proposition de tri : [09-Issues-GitHub.md](09-Issues-GitHub.md).
