# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Commands

### Development
```bash
composer dev        # Start all dev servers (Laravel, queue, logs, Vite) concurrently
composer setup      # First-time setup: install deps, .env, migrate, npm build
```

### Testing
```bash
composer test                                                  # All tests (runs lint:check first)
./vendor/bin/phpunit tests/Feature/SomeTest.php                # Single file
./vendor/bin/phpunit --filter test_method_name                 # Single test
```

### Linting & Type Checking
```bash
composer lint          # PHP: fix with Pint
composer lint:check    # PHP: check only (CI)
npm run lint           # TS/Vue: ESLint fix
npm run format         # JS/Vue/CSS: Prettier fix
npm run types:check    # Vue TSC type check
composer ci:check      # Full CI suite locally
```

### Build
```bash
npm run build          # Production frontend build
npm run build:ssr      # SSR build
```

## Architecture

**Standupr** is a Laravel 13 + Vue 3 + Inertia.js SPA. The backend renders page props, Inertia delivers them to Vue components without full page reloads.

### Backend (`app/`)
- **Controllers** handle form requests and return `Inertia::render()` responses
- **Fortify** powers auth (register, login, password reset, 2FA) — configured in `app/Providers/AppServiceProvider.php` and `config/fortify.php`
- **Actions** hold business logic, invoked from controllers or Fortify hooks

### Frontend (`resources/js/`)
- **Pages** (`pages/`) are full Vue page components matched by Inertia to Laravel routes
- **Layouts** (`layouts/`) — three layouts: `AppLayout`, `AuthLayout`, `SettingsLayout`
- **Components** use Shadcn-Vue (Reka UI based); icons from Lucide
- **Composables** (`composables/`) for shared Vue logic
- **Routes** (`routes/`) — Wayfinder generates typed route helpers from Laravel routes
- Path alias `@/` → `resources/js/`

### Database
- SQLite by default (in-memory SQLite for tests — see `phpunit.xml`)
- All schema changes via Laravel migrations

### Key Stack Versions
- PHP 8.3+, Laravel 13, PHPUnit 12
- Vue 3.5, TypeScript (strict), Vite, Tailwind CSS v4
- ESLint + Prettier (4-space indent, single quotes, 80-char print width)
