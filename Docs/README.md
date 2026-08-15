# Docs — CraftMyWebsite

Documentation interne du **Core** du CMS, guides et fichiers de contexte.

> Le point d'entrée général est [`../CONTEXT.md`](../CONTEXT.md) à la racine du projet.

## Sommaire

| Document | Contenu |
|---|---|
| [01-Ecosysteme.md](01-Ecosysteme.md) | Organisation des dépôts, orgs GitHub, liens, workflow multi-repo |
| [02-Core-Architecture.md](02-Core-Architecture.md) | Cycle de vie, Managers, routing, base de données, vues, events, implémentations |
| [03-Packages.md](03-Packages.md) | Anatomie d'un package + inventaire des packages |
| [04-Themes.md](04-Themes.md) | Anatomie d'un thème, configurateur live, `data-cmw` |
| [05-Dashboard-UI.md](05-Dashboard-UI.md) | Composants UI du panel d'administration |
| [06-Conventions.md](06-Conventions.md) | Conventions de code, namespaces, BDD, i18n |
| [07-Workflow-Dev.md](07-Workflow-Dev.md) | Environnement local, commandes, Git, PR |
| [08-Roadmap-V1.md](08-Roadmap-V1.md) | Décisions actées + chantiers pour la v1 stable |
| [09-Issues-GitHub.md](09-Issues-GitHub.md) | Snapshot et tri des issues ouvertes de l'org |
| [10-CLI.md](10-CLI.md) | Le CLI `php cmw` : commandes, architecture, état réel des générateurs |
| [11-Extension-JetBrains.md](11-Extension-JetBrains.md) | Le plugin IDE CMW : actions, templates, état réel |

## Sources externes

- Documentation publique : https://craftmywebsite.fr/docs
- Schéma JSON de toute la doc : https://apiv2.craftmywebsite.fr/v1/docs/schem
- Doc technique locale (gitignorée) : `.copilot/Technical/`
- Showcase des composants du dash : https://dash.craftmywebsite.fr/

## Convention de rédaction

- Français.
- Ces documents décrivent **comment le Core fonctionne réellement**, pas comment il devrait fonctionner.
  Si une divergence est constatée entre ce dossier et le code, **le code fait foi** — et le document doit
  être corrigé.
- Ne pas dupliquer la doc publique : préférer un résumé + un lien.
