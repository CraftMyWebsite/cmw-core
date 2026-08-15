# 09 — Issues GitHub

> Snapshot du **14/08/2026** — org `CraftMyWebsite`.
> Source : `gh search issues --owner CraftMyWebsite --state open`
> Vue en ligne : https://github.com/issues/assigned?q=org:CraftMyWebsite

## Vue d'ensemble

**52 issues ouvertes**, réparties sur 15 dépôts.

| Dépôt | Ouvertes | Dont bugs | Dans le périmètre v1 |
|---|---|---|---|
| `cmw-core` | 11 | 4 | ✅ **prioritaire** |
| `package-votes` | 7 | 3 | ✅ |
| `package-wiki` | 5 | 2 | ✅ |
| `package-calendar` | 5 | 0 | ✅ (non cloné) |
| `theme-wipe` | 5 | 4 | ✅ |
| `package-shop` | 4 | 0 | ⏸️ 2ᵉ partie |
| `cmw-doc` | 4 | 0 | ✅ |
| `package-forum` | 3 | 2 | ✅ (non cloné) |
| `extension-jetbrains` | 2 | 1 | ⏸️ outillage |
| `cmw-link` | 1 | 0 | ⏸️ plugin Java |
| `cmw-installer` | 1 | 1 | ✅ |
| `package-minecraft` | 1 | 1 | ✅ |
| `package-support` | 1 | 0 | ✅ (non cloné) |
| `theme-nethercraft` | 1 | 0 | ⏸️ |
| `theme-vega` | 1 | 1 | ⏸️ |

**PRs ouvertes (2)** :
- `cmw-doc` #13 — [ADD] BDD Minestrator (@Emilien52, 2025-06-28)
- `package-news` #18 — [IMPROVES] NewsAPI (@SuiramX, 2025-02-05)

**Assignées à @Teyir (4)** : `package-minecraft` #12, `extension-jetbrains` #1, `cmw-core` #375,
`package-wiki` #20.

---

## 1. Core — `cmw-core` (11)

### Bugs (4) — à traiter en priorité v1

| # | Titre | Depuis | Note |
|---|---|---|---|
| **434** | Dossier d'installation non supprimé après une UPDATE | 2026-01 | ⚠️ **Sécurité** — le dossier `Installation/` survit à une mise à jour et déclenche une alerte en page d'accueil. Contournement manuel aujourd'hui. |
| **427** | « Entity too large » à l'upload d'une image trop lourde | 2025-10 | Demande un contrôle de poids **avant** envoi + message clair. Touche `ImagesManager` / `FilesManager`. |
| **413** | Activation forcée de la 2FA par rôle non fonctionnelle | 2025-03 | Les rôles sélectionnés ne sont pas persistés (Utilisateurs > Paramètres). Package **Users**. |
| **419** | Barre latérale : menu en doublon / sélection fantôme | 2025-04 | Dans Utilisateurs > Paramètres > Sécurité, le menu Paramètres se déroule et se colore seul. Thème **Dashboard**. |
| **420** | Barre latérale qui descend mal sur mobile/tablette | 2025-04 | Scroll de la sidebar. Thème **Dashboard**. |

> 419 et 420 concernent tous deux la sidebar du dash → à traiter ensemble, dans le thème `Dashboard`.

### Évolutions (7)

| # | Titre | Depuis |
|---|---|---|
| 346 | Partie upload pour héberger ses images | 2024-09 |
| 369 | Drag sorter elements | 2024-10 |
| 375 | Installer : nom de la BDD | 2024-10 *(assignée @Teyir)* |
| 411 | Pseudo ou nom/prénom à la création du compte | 2025-03 |
| 422 | Page d'erreur personnalisée | 2025-04 |
| 425 | Vérification des adresses mail | 2025-09 |

> #346 recoupe le package **Media** (déjà existant) — à vérifier avant de développer quoi que ce soit.
> #425 (vérification email) est un candidat sérieux pour la v1 : c'est une attente standard d'un CMS.

---

## 2. Packages officiels

### `package-votes` (7)

| # | Type | Titre |
|---|---|---|
| 19 | bug | ID de site non reconnu + récompense serveur non donnée *(2026-02, le plus récent)* |
| 17 | bug | Spam clic pour incrémenter le classement ⚠️ **abus** |
| 9 | bug | Bouton de vote bloqué sur « vérification… » |
| 6 | lang | Repasser sur les fichiers de langue |
| 11 | feat | Récompenses supplémentaires au bout de X votes |
| 13 | feat | Plusieurs récompenses avec pourcentage de chance |
| 18 | feat | Notification Minecraft quand un joueur peut revoter |

### `package-wiki` (5)

| # | Type | Titre |
|---|---|---|
| 24 | bug | Impossible d'insérer une image (1 moyen sur 3 fonctionne) |
| 20 | bug | Renommage d'article sans mise à jour de l'URL *(assignée @Teyir)* |
| 18 | compat | Problème de compatibilité Firefox |
| 19 | feat | Réordonner catégories et articles |
| 21 | feat | Amélioration de l'édition d'article |

### `package-forum` (3) — non cloné

| # | Type | Titre |
|---|---|---|
| 24 | bug | Permissions : créer un topic dans un forum fermé ⚠️ **permissions** |
| 23 | bug | Affichage des bullet lists |
| 20 | i18n | Traductions manquantes |

### `package-calendar` (5) — non cloné

Toutes des feature requests de @X3R0-FR (2025-03), cohérentes entre elles :
événements récurrents (#2), création rapide (#3), tags (#4), description (#5), lieu (#6).
→ Le package semble incomplet : ces 5 issues forment en réalité **un seul chantier**.

### `package-minecraft` (1)

| # | Type | Titre |
|---|---|---|
| 12 | bug | LINK SERVER WITH LETTER *(assignée @Teyir)* |

### `package-support` (1) — non cloné

| # | Type | Titre |
|---|---|---|
| 3 | feat | Supprimer et archiver les demandes de support côté admin |

### `package-shop` (4) — ⏸️ 2ᵉ partie

Moyens de paiement Dedipass (#12) et PaySafeCard manuel (#13), images via URL (#17),
notifications FOMO (#24).

---

## 3. Thèmes

### `theme-wipe` (5)

| # | Type | Titre |
|---|---|---|
| 5 | bug | Déformation des images Wiki |
| 6 | bug | Texte du forum mal positionné |
| 7 | bug | Redirection modif |
| 8 | bug | *(titre vide)* — à qualifier ou fermer |
| 1 | feat | Page de votes |

> Wipe étant le **thème de référence** pour les créateurs de thèmes, ses bugs ont un impact
> pédagogique disproportionné. À assainir avant la v1.

### Autres

`theme-nethercraft` #3 (feature request au titre vide), `theme-vega` #1 (bug register).

---

## 4. Outillage & doc

### `cmw-doc` (4)
#3 tutoriels utilisateur à ajouter · #6 enrichir « Créer un package » *(@Teyir)* ·
#11 mettre à jour le tuto OAuth · #14 tuto installation nginx *(@Teyir)*

### `cmw-installer` (1)
#3 erreur au one-click install — ⚠️ à qualifier, l'installeur conditionne l'expérience v1.

### `extension-jetbrains` (2)
#1 support Webstorm *(@Teyir)* · #5 bug sur le nom des models

### `cmw-link` (1)
#8 compatibilité NeoForge (plugin Java, hors périmètre CMS)

---

## 5. Proposition de tri pour la v1

### Bloc 1 — Bugs bloquants / sécurité
`cmw-core` #434 (dossier Installation après update), `package-forum` #24 (permissions),
`package-votes` #17 (spam clic), `cmw-installer` #3 (one-click install).

### Bloc 2 — Bugs fonctionnels du Core
`cmw-core` #413 (2FA par rôle), #427 (poids d'image), #419 + #420 (sidebar du dash).

### Bloc 3 — Bugs des packages officiels
`package-wiki` #24, #20, #18 · `package-votes` #19, #9 · `package-minecraft` #12 ·
`package-forum` #23 · `theme-wipe` #5, #6, #7, #8.

### Bloc 4 — Qualité perçue
i18n (`package-votes` #6, `package-forum` #20) et documentation (`cmw-doc` ×4).

### Bloc 5 — Évolutions
À arbitrer après les blocs 1-4. `cmw-core` #425 (vérification email) est le meilleur candidat à
remonter dans le périmètre v1.

### À qualifier / fermer
`theme-wipe` #8 et `theme-nethercraft` #3 ont un **titre vide**. `cmw-core` #346 recoupe
probablement le package **Media** existant. Plusieurs issues datent de 2024 et concernent des versions
`alpha-07` / `alpha-08` — **une passe de re-test sur la version courante est nécessaire** avant d'en
traiter le fond : certaines sont probablement déjà résolues.

---

## Mise à jour de ce document

```bash
gh search issues --owner CraftMyWebsite --state open --limit 200 \
  --json repository,number,title,labels,createdAt,author,url
```
