# 06 — Conventions

> Référence officielle : https://craftmywebsite.fr/docs/fr/technical/conventions/general
> Tout code ne respectant pas ces conventions peut être refusé en PR.

## 1. Règles générales

- **Le code est rédigé en anglais** — y compris les commentaires et la PHPDoc.
  (La *documentation* du projet, elle, est en français.)
- Nommage explicite pour tout ce qui est global (fonctions, variables importantes).
- Base : **PSR-12**.
- PHPDoc encouragée, notamment partout où l'IDE signale une ambiguïté.

## 2. Nommage

| Élément | Convention | Précision |
|---|---|---|
| Classes | `PascalCase` | Doit commencer par le nom du package + sa fonction — ex. `FaqCategoryModel` |
| Méthodes, fonctions, variables, propriétés | `camelCase` | Typer les paramètres **et** le retour dès que possible |
| Constantes | `SCREAMING_SNAKE_CASE` | |
| Tables & colonnes SQL | `snake_case` | Voir § 4 |

Suffixes de classe :

| Rôle | Suffixe | Classe parente |
|---|---|---|
| Contrôleur | `{Feature}Controller` | `AbstractController` |
| Modèle | `{Feature}Model` | `AbstractModel` |
| Entité | `{Feature}Entity` | `AbstractEntity` |
| Événement | `{Feature}Event` | `AbstractEvent` |
| Exception | `{Feature}Exception` | |
| Interface | `I{Feature}` | |

Exemple canonique :

```php
class ExampleModel extends AbstractModel
{
    const string TABLE_NAME = 'exemple_table';

    public int $exempleId;
    public string $exempleColumnName;

    public function linkWithOther(int $otherId): int
    {
        $params = ['otherId' => $otherId, 'exempleId' => $this->exempleId];
        $sql = 'INSERT INTO `TABLE_NAME`(exemple_table, other_table) VALUES (:exempleId, :otherId)';
        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);
        return $req->execute($params) ? $categoryId : -1;
    }
}
```

## 3. Namespaces

**Tous les namespaces commencent par `CMW`.**

### Packages

| Élément | Namespace |
|---|---|
| Controllers | `CMW\Controller\{PACKAGE}` |
| Entities | `CMW\Entity\{PACKAGE}` |
| Events | `CMW\Event\{PACKAGE}` |
| Exception | `CMW\Exception\{PACKAGE}` |
| Implementations | `CMW\Implementation\{PACKAGE}\{PACKAGE_INTERFACE}` |
| Permissions | `CMW\Permissions\{PACKAGE}` |
| Interfaces | `CMW\Interface\{PACKAGE}` |
| Models | `CMW\Model\{PACKAGE}` |
| Type | `CMW\Type\{PACKAGE}` |
| Package.php | `CMW\Package\{PACKAGE}` |

### Thèmes

`CMW\Theme\{THEME}`

### Core

| Élément | Namespace |
|---|---|
| Managers | `CMW\Manager\{FOLDER_NAME}` |
| Utils | `CMW\Utils` |

## 4. Base de données

- Format **`snake_case`**, **en anglais uniquement**.
- Toute table **commence par `cmw_`** — ex. `cmw_users`, `cmw_votes_votepoints`.
- Toute table possède **au minimum une clé primaire**.
- Les colonnes numériques à sémantique implicite (`tinyint` servant d'état, etc.) doivent être
  **commentées** en base.

### Rappel du nom de table dans les colonnes

Format : `cmw_<tableName>_<columnName>`

| Type de colonne | Exemple |
|---|---|
| Colonne normale → référence **la table courante** | `cmw_users_username` |
| Clé étrangère → référence **la table étrangère** | `cmw_articles_user_id` |

## 5. Emplacement des fichiers dans un package

| Type | Fichier | Emplacement |
|---|---|---|
| Contrôleur admin | `ExampleController.php` | `{PACKAGE}/Controllers/Admin` |
| Contrôleur public | `ExampleController.php` | `{PACKAGE}/Controllers/Public` |

> ⚠️ **Écart connu entre la convention et l'existant** (constaté le 15/08/2026) : la séparation
> `Controllers/Admin` + `Controllers/Public` n'est réellement appliquée que par **News** et **Votes**.
> **Core**, **Pages**, **Faq**, **Wiki**, **Media** et **Minecraft** ont leurs contrôleurs **à plat**
> dans `Controllers/` ; **Users** a un `Admin/` mais garde 10 contrôleurs à plat à côté.
> La convention ci-dessus reste la cible pour tout nouveau code ; l'alignement de l'existant est un
> chantier de refonte à part.
| Entité | `ExampleEntities.php` | `{PACKAGE}/Entities` |
| Événement | `ExampleEvent.php` | `{PACKAGE}/Events` |
| Exception | `ExampleException.php` | `{PACKAGE}/Exception` |
| Implémentation | `ExampleFooImplementations.php` | `{PACKAGE}/Implementations` |
| Permissions | `Permissions.php` | `{PACKAGE}/Init` |
| Interface | `IExample.php` | `{PACKAGE}/Interfaces` |
| Langues | `fr.php`, `en.php` | `{PACKAGE}/Lang` |

Thèmes : dossiers en `Majuscule + minuscules`, fichiers **tout en minuscules** sauf `Theme.php`.
Vues : `*.view.php` — includes : `*.inc.php`.

## 6. Permissions

Format : `{package}.{feature}.{action}`

```
core.settings.website
core.themes.market
users.manage.edit
faq.show
```

Déclaration dans `{PACKAGE}/Init/Permissions.php`, vérification via
`UsersController::hasPermission('users.manage.edit')` ou `redirectIfNotHavePermissions(...)`.

## 7. Internationalisation (i18n)

- **Aucun texte visible par l'utilisateur ne doit être en dur.**
- Fichiers : `{PACKAGE}/Lang/fr.php` et `{PACKAGE}/Lang/en.php` (tableaux associatifs imbriqués).
- Appel : `LangManager::translate('faq.menu.title')` — le premier segment est le nom du package
  en minuscules.
- Le CLI a ses propres langs : `App/Cli/Utils/Lang/{fr,en}.php`.
- Doc : https://craftmywebsite.fr/docs/fr/technical/i18n/panel-admin

## 8. Formatage

**Avant chaque PR :**

```bash
php pretty-php.phar *
```

Le `.editorconfig` à la racine (très détaillé) fait également autorité.
