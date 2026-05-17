# Architecture

This website is a **modular monolith**. Each business domain lives under `src/Module/<Name>/` and is built to be extracted later by copy/paste — not as a Composer package, not as a DDD/hexagonal stack. Modules follow the standard Symfony layout (Controller/, Entity/, Repository/, Form/, Twig/Component/, templates/).

## Folder Layout

```
src/
├── Module/                # one folder per business domain
│   └── <Name>/
│       ├── Controller/
│       ├── Entity/
│       ├── Repository/
│       ├── Form/
│       ├── Twig/Component/
│       └── templates/     # @<Name> namespace
├── Page/                  # dynamic, block-based pages (over, API, uitrusting, ...)
│   ├── Controller/
│   ├── Entity/
│   └── templates/         # @Page namespace
└── Shared/                # cross-cutting code only
    ├── Entity/            # UlidIdTrait, TimestampableTrait + interfaces
    ├── Security/          # PermissionMapProvider, voters (planned)
    ├── Content/           # block-based content system (planned)
    └── templates/         # @Shared namespace (base.html.twig, _header, _footer)
```

There is **no `templates/` directory at the project root**. All templates live inside their owner module/page/shared.

## Planned modules

News, Team (Staff + Rider), Program, Results (uitslagen), Sponsor, Contact, Menu.

## Isolation rules

- A module NEVER imports a class from another module's `Entity/`, `Controller/`, `Form/`, etc.
- Cross-module needs go through `src/Shared/` (interfaces, value objects, traits).
- If real coupling is needed, expose an interface from the producer module and inject it.
- Discipline is manual for now; install `deptrac` if violations appear.

## URL & Routing Conventions

- Public URLs are in **Dutch** (`/nieuws/{slug}`, `/ploeg`, `/programma`, `/uitslagen`, `/sponsors`, `/contact`, `/over`, ...).
- Code (namespaces, route names) is in **English** (`App\Module\News\Controller\ShowArticleController`, route name `news_show`).
- Route names follow `<module>_<action>` pattern.
- `Article.slug` is editable (auto-suggested from title); the route path is fixed `/nieuws/{slug}`.
- `Page.path` is completely editable; resolved by a catch-all controller with low priority.

## Permissions (planned)

Domain-based enum permissions + dual voters (role-based + ownership-based). See [`permissions.md`](./permissions.md). Each module owns its `XxxPermissions` enum; `PermissionMapProvider` is centralized in `src/Shared/Security/`.

## Content blocks (planned)

Contentful entities (`Article`, `Page`) store body as a JSON list of structured blocks (text with WYSIWYG + positioned image, heading, image, gallery, video, quote, divider, cta, embed). Rendered via Twig components; edited via Live Components. Lives in `src/Shared/Content/`. **No markdown anywhere** — block-based editing is the contract for end-user friendliness.

## Admin

- Custom per module — NO EasyAdmin, NO Sonata.
- Each module ships its own admin controllers + Forms + Twig (Live Components for interactivity).

## i18n

- UI translated via Symfony Translator (`translations/messages.<locale>.yaml`).
- `default_locale: nl`, `enabled_locales: [nl]` (add `fr` later if needed).
- User-generated content is stored AS-IS in the creator's language — no `locale` column on entities, no translation tables.

## Entities

- Use ULID as identifier (via `App\Shared\Entity\UlidIdTrait`).
- Immutable timestamps (`\DateTimeImmutable`) for `createdAt` / `updatedAt`, managed via `App\Shared\Entity\TimestampableTrait`.
- Implement `TimestampableInterface` and add `#[ORM\HasLifecycleCallbacks]` on the entity to wire the trait's PrePersist/PreUpdate hooks.

## Deployment

- Target: **Coolify** on a private server (NOT Upsun / Platform.sh).
- Production image: FrankenPHP (Dockerfile to be added).
- Storage: Flysystem with `local` adapter against a Coolify-mounted volume; MinIO sidecar for S3-compatible storage if needed.

## Testing

- Minimum **80% line coverage** enforced by `make coverage-gate` (pcov + `tools/coverage-check.php`).
- Test directory mirrors `src/`: `tests/Module/<Name>/`, `tests/Page/`, `tests/Shared/`.
- Foundry stories for fixtures; DAMA Doctrine bundle wraps tests in transactions.
- Prefer integration tests via `WebTestCase` for controllers; unit tests for isolated logic.
