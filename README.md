# VMLCT Website

Van Moer Logistics Cycle Team — public website and CMS, built with Symfony 8.

## Stack

- PHP 8.4+ (with `pcov` for coverage)
- Symfony 8
- Doctrine ORM 3 + MariaDB 11
- Redis (cache, sessions)
- AssetMapper + Stimulus + Turbo + UX Icons + UX Twig Components
- PHPUnit 13 + Zenstruck Foundry + DAMA Doctrine Test Bundle
- PHPStan (level 8), PHP-CS-Fixer, Rector

## Requirements

- PHP 8.4+ with the standard Symfony extensions and `pcov`
- [Symfony CLI](https://symfony.com/download)
- Docker + Docker Compose (for MariaDB and Redis)
- Composer

## Quick start

```bash
make install   # composer install + run migrations
make run       # start Docker services + Symfony server (https://127.0.0.1:8000)
make abort     # stop everything
```

The Symfony CLI handles TLS, hot reload, and worker management (see `.symfony.local.yaml`).

### Dev fixtures

```bash
symfony console foundry:load-fixtures users     # seed admin/editor/user accounts
symfony console foundry:load-fixtures sponsors  # seed sponsors
```

Dev credentials seeded by the `users` story:

| Email                | Password | Roles              |
|----------------------|----------|--------------------|
| admin@vmlct.local    | admin    | `ROLE_SUPER_ADMIN` |
| editor@vmlct.local   | editor   | `ROLE_ADMIN`       |
| (faker)              | user     | `ROLE_USER`        |

Admin URL: `/admin` (requires `ROLE_ADMIN`). Login at `/login`.

## Architecture

The project is a **modular monolith**. Each business domain lives in `src/Module/<Name>/` and is built to be extractable later by copy/paste — not as a Composer bundle.

```
src/
├── Module/        # one folder per business domain (News, Team, Program, ...)
├── Page/          # dynamic block-based pages (over, API, uitrusting, ...)
└── Shared/        # cross-cutting code (entity traits, security, content blocks)
```

Public URLs are in Dutch; code and route names are in English. No `templates/` at the project root — every template lives inside its owner module (`@<Name>`, `@Page`, `@Shared` Twig namespaces).

See [`docs/architecture.md`](./docs/architecture.md) for the full architecture brief and conventions, and [`docs/permissions.md`](./docs/permissions.md) for the permission system design.

## Useful commands

```bash
make help               # list all targets

# Database
make db-reset           # drop + create + migrate
make db-diff            # generate a migration from entity changes

# Code quality
make lint               # YAML + container + Twig + composer
make analyze            # php-cs-fixer (dry-run) + phpstan
make fix                # apply php-cs-fixer
make rector             # rector dry-run
make rector-fix         # apply rector

# Tests
make test               # phpunit
make test-coverage      # phpunit with HTML + text coverage report (in var/coverage/)
make coverage-check     # fail if coverage < 80%
make coverage-gate      # test-coverage + coverage-check

# CI
make ci                 # full pipeline (lint + analyze + test)
```

## Tests

The test suite uses PHPUnit, Foundry stories for fixtures, and DAMA Doctrine Test Bundle for automatic transaction rollback. Coverage target is **80% lines**, enforced by `tools/coverage-check.php`.

```
tests/
├── Module/<Name>/      # mirrors src/Module/<Name>/
├── Page/               # mirrors src/Page/
└── Shared/             # mirrors src/Shared/
```

## Deployment

The website is deployed via [Coolify](https://coolify.io/) on a private server, using a FrankenPHP image (Dockerfile to be added).

## License

Proprietary.
