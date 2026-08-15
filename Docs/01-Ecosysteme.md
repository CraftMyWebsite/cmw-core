# 01 — Écosystème et organisation des dépôts

## Principe

CMW est un CMS **modulaire multi-dépôts** :

- un **Core** (ce dépôt) contenant le moteur + 3 packages non supprimables + le thème par défaut ;
- des **packages** et des **thèmes** indépendants, chacun dans son propre dépôt Git, clonés dans
  l'arborescence du Core pour le développement local.

```
cmw-core (ce repo)
├── App/Package/Core      ← intégré
├── App/Package/Users     ← intégré
├── App/Package/Pages     ← intégré
├── App/Package/News      ← repo externe (CraftMyWebsite/package-news)
├── App/Package/…         ← repos externes
├── Public/Themes/Sampler ← intégré (thème public par défaut)
├── Public/Themes/Wipe    ← repo externe (thème d'exemple)
└── Public/Themes/Dashboard ← repo externe (thème du panel admin)
```

## Organisations GitHub

| Org | Rôle |
|---|---|
| [`CraftMyWebsite`](https://github.com/CraftMyWebsite) | **Officiel** — Core, doc, packages et thèmes maison |
| [`CraftMyWebsitePro`](https://github.com/CraftMyWebsitePro) | **Interne/premium** — API, thème du dash, packages propriétaires |
| [`Overheat-Studio-Community`](https://github.com/Overheat-Studio-Community) | **Non officiel** — packages/thèmes partenaires, distribués sur le Marketplace |

> Les packages Overheat Studio ne sont **pas officiels CMW**, mais ils sont proposés sur le
> Marketplace. Ils ne conditionnent pas la sortie de la v1.

## Cartographie complète (état au 14/08/2026)

### Org `CraftMyWebsite` — dépôts publics

| Dépôt | Rôle | En local |
|---|---|---|
| `cmw-core` | **Core du CMS** | ✅ (ce repo) |
| `cmw-doc` | Documentation FR/EN (Technical + Users) | ❌ |
| `cmw-installer` | Installeur one-click | ❌ |
| `cmw-link` | Plugin Java serveurs MC (Spigot / Bungeecord / Velocity) | ❌ |
| `cmw-theme-tailwindcss-template` | Template de thème TailwindCSS | ❌ |
| `extension-jetbrains` | Plugin IDE JetBrains (PhpStorm, IntelliJ) — génère packages, thèmes, managers. Voir [11](11-Extension-JetBrains.md) | ❌ |
| `package-calendar` | Package Calendrier | ❌ |
| `package-contact` | Package Contact | ❌ |
| `package-faq` | Package FAQ | ✅ |
| `package-forum` | Package Forum | ❌ |
| `package-minecraft` | Package Minecraft | ✅ |
| `package-news` | Package News | ✅ |
| `package-newsletter` | Package Newsletter | ❌ |
| `package-redirect` | Package Redirect | ✅ |
| `package-shop` | **Package E-commerce** — expérimental, chantier « 2ᵉ partie » | ❌ |
| `package-support` | Package Support / tickets | ❌ |
| `package-votes` | Package Votes | ✅ |
| `package-wiki` | Package Wiki | ✅ |
| `theme-nethercraft`, `theme-nightcraft`, `theme-pixcraft`, `theme-rainfall`, `theme-vega`, `theme-wipe` | Thèmes publics | ⚠️ seul `Wipe` |
| `CMW` | **Archivé** — repo de la V1 historique | ❌ |

> ℹ️ Ce document ne liste **que les dépôts publics**. Les dépôts privés sont mentionnés de façon
> agrégée : ce fichier est versionné dans `cmw-core`, qui est public.

### Org `CraftMyWebsite` — dépôts privés

L'org compte également plusieurs dépôts privés (bot Discord, packages et thèmes non publics),
dont **Media** et **Rcon** qui sont présents en local.

### Org `CraftMyWebsitePro` — tous privés

Infrastructure et briques propriétaires : API publique 2.0, service de logs, packages internes
(licence, market, maintenance…), thème du site et **thème `Dashboard`** (présent en local).

> Ces dépôts relèvent d'un **contexte séparé** (API, site, back-office interne).
> Pour l'inventaire nominatif : `gh repo list CraftMyWebsitePro`.

### Org `Overheat-Studio-Community`

Publics : `cmw-package-LoginFastImplementation`, `cmw-package-OverEnv` ✅,
`cmw-package-litebans` ✅, `cmw-package-overapi` ✅, `cmw-package-restricted`,
`cmw-package-simple-cookies` ✅, `cmw-package-sitemap_explorer` ✅,
`cmw-theme-Landingify`, `cmw-theme-feather`.

Privés : plusieurs packages et thèmes, dont **NewsPro** (premium) et **OverTranslations** (payant),
tous deux présents en local.

> ⚠️ **Écart local ↔ GitHub** : plusieurs packages officiels ne sont pas clonés en local
> (`calendar`, `contact`, `forum`, `newsletter`, `shop`, `support`) ainsi que la majorité des thèmes.
> Ils devront l'être pour être « v1-ready » — voir [08-Roadmap-V1.md](08-Roadmap-V1.md).

## Dépôts présents en local

### Core

| Chemin | Dépôt | Branches |
|---|---|---|
| `.` | `CraftMyWebsite/cmw-core` | dev (développement) → main (production) |

### Packages — org `CraftMyWebsite`

| Package | Dépôt | Branche |
|---|---|---|
| Faq | `package-faq` | main |
| Media | *(dépôt privé)* | main |
| Minecraft | `package-minecraft` | main |
| News | `package-news` | main |
| Rcon | *(dépôt privé)* | main |
| Redirect | `package-redirect` | main |
| Votes | `package-votes` | main |
| Wiki | `package-wiki` | main |

### Packages — org `Overheat-Studio-Community`

| Package | Dépôt | Branche |
|---|---|---|
| Litebans | `cmw-package-litebans` | main |
| NewsPro | *(dépôt privé, premium)* | main |
| OverApi | `cmw-package-overapi` | main |
| OverEnv | `cmw-package-OverEnv` | main |
| OverTranslations | *(dépôt privé, payant)* | main |
| SimpleCookies | `cmw-package-simple-cookies` | main |
| SitemapExplorer | `cmw-package-sitemap_explorer` | main |

### Thèmes

| Thème | Dépôt | Rôle |
|---|---|---|
| Sampler | *(dans le Core)* | Thème public **par défaut**, installé de base |
| Wipe | `CraftMyWebsite/theme-wipe` | Thème public de **référence / exemple** |
| Dashboard | *(dépôt privé, org `CraftMyWebsitePro`)* | Thème du **panel admin** ([showcase](https://dash.craftmywebsite.fr/)) |

## Documentation

| Contenu | Dépôt |
|---|---|
| Doc publique (FR/EN, Technical + Users) | `CraftMyWebsite/cmw-doc` — à cloner à côté du Core |

**Règle : toute modification fonctionnelle doit être répercutée dans `cmw-doc`.**

Structure : `FR/` et `EN/`, chacun avec `Technical/` et `Users/`, plus `Assets/` (images).
Les dossiers sont préfixés `[01]`, `[02]`… pour l'ordre d'affichage. 75 fichiers `.md` au total —
l'anglais est très en retard sur le français (2 pages contre 73).

L'API expose le schéma complet de la doc :
`GET https://apiv2.craftmywebsite.fr/v1/docs/schem`

```jsonc
{
  "FR": {
    "Technical": {
      "Commencer": [
        { "path": "…/[01]Introduction.md",
          "slug": "technical/commencer/introduction",
          "title": "Introduction",
          "editUrl": "https://github.com/CraftMyWebsite/cmw-doc/tree/main/…" }
      ]
    }
  },
  "EN": { … }
}
```

Le `path` renvoie directement le Markdown brut → utile pour consulter une page de doc précise
sans passer par le site.

La copie locale `.copilot/Technical/` est **gitignorée** et générée par `php cmw ai-copilot`.
Un mécanisme de synchronisation vers `Docs/` reste à mettre en place (script ou lien vers `cmw-doc`) —
voir [08-Roadmap-V1.md](08-Roadmap-V1.md).

## Conséquences pratiques

### Exclusion Git

Le `.gitignore` du Core exclut explicitement tout ce qui n'est pas core :

```gitignore
!/App/Package
/App/Package/*
!/App/Package/Core
!/App/Package/Users
!/App/Package/Pages

/Public/Themes/*
!/Public/Themes/Sampler
```

### Règle d'or avant tout commit

**Vérifier dans quel dépôt on écrit.** Une modification dans `App/Package/Wiki/` doit être committée
depuis ce sous-dossier :

```bash
git -C App/Package/Wiki status
git -C App/Package/Wiki add -A && git -C App/Package/Wiki commit -m "…"
```

Un `git status` à la racine ne montrera **jamais** ces changements.

### Changements cassants

En cas de changement cassant introduit dans le Core, la règle est de faire une **passe de refacto sur
tous les packages présents en local** dans la foulée. Sinon, on travaille au cas par cas.

### Fichiers locaux de désactivation

| Fichier | Rôle |
|---|---|
| `.ignored_packages` | Liste (1 nom par ligne) des packages ignorés au chargement |
| `.ignored_themes` | Idem pour les thèmes |

Gitignorés, utilisés pour désactiver temporairement un module en dev local.

### Distribution

Packages et thèmes sont distribués via le **Marketplace** intégré au panel admin
(`ThemeMarketManager`, `App/Manager/Download`, `PublicAPI`), qui interroge l'API CMW (`APIURL`).
Le CMS vérifie aussi les mises à jour du Core via `UpdatesManager` / `CMSUpdaterManager`.
