# 📄 Documentation de configuration des thèmes (EditorMenu)

Ce fichier permet de définir dynamiquement les options de personnalisation d'un thème via l'éditeur visuel.
Chaque menu (EditorMenu) contient un groupe de valeurs (EditorValue), affichables dans le builder et modifiables en live.

---

## 🔖 Structure générale

```php
new EditorMenu(
    title: 'Nom affiché dans l’UI',
    key: 'identifiant_unique',
    scope: 'chemin/url/optionnel',
    requiredPackage: 'NomDuPackage' // ou null
    values: [EditorValue(...), ...]
)
```

Chaque `EditorValue` définit une clé, un type, une valeur par défaut, et parfois des options (ex: select ou range).

---

## 💡 Types disponibles

| Type         | Constante                | Description                                                                 |
|--------------|---------------------------|-----------------------------------------------------------------------------|
| Texte        | `EditorType::TEXT`        | Champ de texte libre                                                        |
| Zone de texte| `EditorType::TEXTAREA`    | Texte multilignes                                                          |
| Nombre       | `EditorType::NUMBER`      | Nombre sans unité                                                         |
| Booléen      | `EditorType::BOOLEAN`     | Case à cocher (on/off)                                                  |
| Couleur      | `EditorType::COLOR`       | Picker de couleur                                                          |
| Image        | `EditorType::IMAGE`       | Upload et prévisualisation d’image                                       |
| CSS libre    | `EditorType::CSS`         | Zone personnalisable CSS                                                   |
| Liste        | `EditorType::SELECT`      | Menu déroulant avec options définies                                     |
| Curseur      | `EditorType::RANGE`       | Slider personnalisable (avec min, max, step, prefix/suffix)               |

---

## 🎨 Exemples d'utilisation dans les vues

### 🔍 Remplacer un texte dynamiquement
```html
<h1 data-cmw="header:site_title"></h1>
```

### 🌈 Appliquer une couleur dynamique
```html
<span data-cmw-style="color:header:text_color"></span>
```

### 🖌️ Appliquer des classes dynamiques (ex: font, layout)
```html
<body data-cmw-class="global:main_font header:grid_layout"></body>
```

### 🚀 Gérer un attribut dynamique (ex: image)
```html
<img data-cmw-attr="src:global:site_image alt:global:image_alt">
```

### 📉 Afficher ou masquer un bloc
```html
<div data-cmw-visible="header:show_title">...</div>
```

---

## 🔄 Exemple complet : Menu Global
```php
new EditorMenu(
    title: 'Globaux',
    key: 'global',
    scope: null,
    requiredPackage: null,
    values: [
        new EditorValue(
            title: 'Police',
            themeKey: 'main_font',
            defaultValue: 'font-montserrat',
            type: EditorType::SELECT,
            selectOptions: [ new EditorSelectOptions(value: 'font-angkor', text: 'Angkor'), ... ]
        ),
        new EditorValue(
            title: 'Image du site',
            themeKey: 'site_image',
            defaultValue: 'Config/Default/Images/logo.png',
            type: EditorType::IMAGE
        ),
        new EditorValue(
            title: 'Taille image',
            themeKey: 'site_image_width',
            defaultValue: '40',
            type: EditorType::RANGE,
            rangeOptions: [ new EditorRangeOptions(0, 256, 1, '', 'px') ]
        ),
        new EditorValue(
            title: 'ALT Image',
            themeKey: 'image_alt',
            defaultValue: 'logo.png',
            type: EditorType::TEXT
        )
    ]
)
```

---

## 🌍 Utilisation dans les fichiers de thème

- Les valeurs sont injectées automatiquement via `View::replaceThemeValues()` en public
- En mode éditeur, les attributs `data-cmw-*` permettent une mise à jour live avec JS

---

## 📚 Bonnes pratiques

- Toujours garder des `themeKey` uniques par menu
- Utiliser des `prefix/suffix` pour les classes et unités CSS dans les sliders
- Prévoir des valeurs par défaut réalistes pour chaque champ
- Garder une logique prévisible pour le builder (polices, couleurs, tailles, visibilité)

---

> ✉️ Pour toute extension ou suggestion : pensez à documenter les nouveaux types ou comportements dans ce fichier.