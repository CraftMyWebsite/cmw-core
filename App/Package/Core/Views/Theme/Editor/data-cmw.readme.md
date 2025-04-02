# ✨ Utilisation de `data-cmw`

Cette documentation résume toutes les possibilités offertes par le système `data-cmw` pour créer des thèmes dynamiques, compatibles avec l'éditeur visuel du CMS.

---

## 📄 Attributs disponibles

| Attribut HTML             | Syntaxe                                       | Description                                                                         | Exemple                                                                                         |
|---------------------------|-----------------------------------------------|-------------------------------------------------------------------------------------|-------------------------------------------------------------------------------------------------|
| `data-cmw`                | `menu:key`                                    | Remplace le contenu textuel (`textContent`) de l'élément                            | `<span data-cmw="header:site_title"></span>` → `Mon Site`                                     |
| `data-cmw-attr`           | `attribut:menu:key [...]`                     | Modifie un ou plusieurs attributs HTML (`src`, `href`, `alt`, etc.)                 | `<img data-cmw-attr="src:global:logo alt:global:logo_alt">`                                    |
| `data-cmw-style`          | `propriété:menu:key [...]`                    | Applique dynamiquement un ou plusieurs styles CSS (avec gestion de `url()`)         | `<div data-cmw-style="background-image:global:banner">` → `style="background-image: url(...)"` |
| `data-cmw-class`          | `menu:key [...]`                              | Ajoute dynamiquement une ou plusieurs classes CSS                                   | `<body data-cmw-class="global:main_font global:padding">` → `font-montserrat px-2`            |
| `data-cmw-visible`        | `menu:key`                                    | Affiche ou masque l'élément selon une valeur booléenne                              | `<div data-cmw-visible="header:show_title">`                                                   |
| `__CMW:menu:key__`        | `__CMW:menu:key__`                            | Remplace dynamiquement dans les scripts JS / JSON                                   | `const max = __CMW:game:max_players__`                                                         |

---

## 🧠 Règles intelligentes de transformation automatique

| Type de valeur dans la config | Traitement automatique                                     |
|-------------------------------|------------------------------------------------------------|
| `#ffffff` ou `red`            | Utilisé directement pour les propriétés de couleur         |
| `https://...`                 | Converti automatiquement en `url('...')` pour les backgrounds |
| `font-montserrat`             | Utilisé tel quel comme class CSS                          |
| `0`, "0" ou `""`            | Cache les éléments avec `data-cmw-visible`                |

---

## 📆 Exemple complet d'utilisation

```html
<div
  data-cmw-visible="section:show_banner"
  data-cmw-class="global:main_font global:padding"
  data-cmw-style="background-image:global:banner_url; font-size:global:font_size"
  class="text-center"
>
  <h1 data-cmw="section:title"></h1>
</div>
```

---

## 💡 Bonnes pratiques

- Toujours préfixer les valeurs dans `configValues` avec `menu_key`, exemple : `site_title` dans le menu `header` 
- Prévoir des valeurs par défaut pour chaque champ configuré dans le thème

---

Pour toute extension de ce système, pensez à respecter la convention `data-cmw-*` afin de garantir la compatibilité avec le builder dynamique.

