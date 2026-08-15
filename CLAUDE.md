# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

> **📖 Contexte complet du projet : lire [`CONTEXT.md`](CONTEXT.md) à la racine, puis [`Docs/`](Docs/README.md).**
> Ces fichiers couvrent l'écosystème multi-dépôts (Core / packages / thèmes), l'architecture du Core,
> les conventions, l'UI du dashboard et la roadmap v1.
>
> ⚠️ **Multi-dépôts** : seuls `App/Package/{Core,Users,Pages}` et `Public/Themes/Sampler` appartiennent
> au dépôt Core. Tous les autres packages et thèmes sont des dépôts Git indépendants — vérifier la cible
> avant tout commit (`git -C App/Package/X status`).

## Project Overview

CraftMyWebsite (CMW) is a professional CMS designed for gaming communities, providing a complete platform for game servers with e-commerce capabilities. The project is currently in ALPHA (v2.0.0-alpha).

**Important:** This is a French project. Most documentation, comments, and language files are in French.

## Development Commands

### Code Formatting
Before every pull request, format code with:
```bash
php pretty-php.phar *
```

### TailwindCSS Compilation (Admin Dashboard)
Watch and compile Tailwind for the admin panel:
```bash
npm run tw-core
```

### CLI Tool
The project includes a CLI tool accessible via:
```bash
php cmw [command]
```

Available commands:
- `theme-init` - Create a new theme structure
- `package-init` - Create a new package structure
- `ai-copilot` - Generate/update GitHub Copilot context

## Architecture

### Package System
CMW uses a modular package architecture. Each package is self-contained in `App/Package/{PackageName}/`:

```
App/Package/{PackageName}/
├── Controllers/     # Route handlers with #[Link] attributes
├── Models/          # Database interaction layer
├── Entities/        # Data objects
├── Views/           # Admin panel views
├── Public/          # Public-facing views
├── Init/            # Permissions, SQL initialization
├── Lang/            # Translations (fr.php, en.php)
├── Implementations/ # Interface implementations for core features
└── Package.php      # Package configuration (IPackageConfigV2)
```

Core packages (cannot be removed):
- **Core** - System core, routing, settings, themes, packages
- **Users** - Authentication, roles, permissions, 2FA

### Routing with Attributes
Routes are defined using PHP attributes on controller methods:

```php
#[Link('/path', Link::GET, [], '/scope')]
#[Link('/admin/path', Link::POST, ['id' => '[0-9]+'], '/cmw-admin/users')]
private function methodName(): void {
    // Implementation
}
```

- Routes are automatically discovered via reflection (see `Loader::loadAttributes()`)
- Admin routes use `/cmw-admin/` scope
- Route parameters are defined with regex patterns

### Managers System
Core functionality is in `App/Manager/`:
- **Router** - AltoRouter-based routing with attribute discovery
- **Database** - PDO wrapper with query builder
- **Env** - Environment configuration management
- **Security** - Encryption, rate limiting, honey inputs
- **Theme** - Theme management and editor
- **Package** - Package loading and management
- **Flash** - Flash messages for user feedback
- **Permission** - Role-based permission system
- **Loader** - Autoloading, attributes scanning, implementations loading

### Implementation Pattern
CMW uses a weight-based implementation system for extensibility. Packages can provide implementations of core interfaces, and the highest weight implementation is used:

```php
Loader::getHighestImplementation(IInterface::class)
```

Implementations are stored in `App/Package/{Package}/Implementations/{Category}/{Implementation}.php`

### View System
Views are rendered through the View manager:

```php
// Admin view
View::createAdminView('PackageName', 'viewName')
    ->addVariableList(['key' => $value])
    ->addStyle('path/to/style.css')
    ->addScriptAfter('path/to/script.js')
    ->view();

// Public view
View::createPublicView('PackageName', 'viewName')->view();
```

### Theme System
Themes are in `Public/Themes/{ThemeName}/`:
- `Theme.php` - Implements `IThemeConfigV2`
- `router.php` - Custom theme routes
- Package-specific view files

### Database
- Uses PDO with prepared statements
- Models extend `AbstractModel`
- Entities extend `AbstractEntity` with typed properties
- Connection managed by `DatabaseManager`

### Security Features
- **Encryption**: `EncryptManager` for sensitive data (emails, tokens)
- **Password Hashing**: BCrypt via `password_hash()`
- **2FA**: TOTP implementation via `TwoFaManager`
- **Rate Limiting**: `RateLimiter` for endpoint protection
- **CSRF**: Honey inputs via `HoneyInput`
- **Permissions**: Role-based with `PermissionManager`

### Events System
Event-driven architecture using `Emitter` and `Listener`:

```php
Emitter::send(EventClass::class, $data);
```

Events extend `AbstractEvent` and are listened to by implementations.

## Environment Variables

Required `.env` file (see `.env.example` if available):
- `DIR` - Project root directory
- `APIURL` - CMW API endpoint
- `SALT` - General encryption salt
- `SALT_PASS` - Password encryption salt
- `SALT_IV` - Initialization vector for encryption
- `INSTALLSTEP` - Installation progress (-1 = installed)
- `DEVMODE` - Development mode flag
- `TIMEZONE` - Default: Europe/Paris

## Conventions

### Code Formatting
- Follow the project's .editorconfig settings
- Use `pretty-php.phar` before committing
- Refer to: https://craftmywebsite.fr/docs/fr/technical/conventions/general

### Naming Conventions
- Controllers: `{Feature}Controller` extending `AbstractController`
- Models: `{Feature}Model` extending `AbstractModel`
- Entities: `{Feature}Entity` extending `AbstractEntity`
- Namespaces follow PSR-4: `CMW\{Type}\{Package}\{Class}`

### Permissions
- Format: `{package}.{feature}.{action}`
- Example: `users.manage.edit`, `core.dashboard`
- Check with: `UsersController::hasPermission('permission.code')`

### Translations
Use `LangManager::translate('package.key')` for all user-facing strings. Translation files are in `App/Package/{Package}/Lang/{locale}.php`.

## Git Workflow

- Main development branch: `dev`
- Production branch: `main`
- Always create PRs from `dev` to `main`

## Key Files to Check

- `App/Manager/Loader/Loader.php` - Understand attribute loading and autoloading
- `App/Manager/Router/Router.php` - Routing implementation
- `App/Manager/Package/AbstractController.php` - Base controller functionality
- `index.php` - Application entry point
- `cmw` - CLI entry point

## Notes

- The project uses standalone environments for CLI operations
- Installation files in `Installation/` are deleted after setup (unless DEVMODE)
- Composer autoload is loaded if available (`vendor/autoload.php`)
- Admin panel uses Tailwind CSS with Flowbite components
- Public themes have independent styling systems
