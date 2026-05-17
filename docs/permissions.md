# Permissions System

Domain-based permission system using enum permissions and specialized Symfony voters.

## Architecture

```
src/Security/
├── Permissions/                           # One enum per business domain
│   ├── PermissionInterface.php            # Contract: extends BackedEnum + isOwnable()
│   └── UserPermissions.php                # USER_VIEW, USER_CREATE, USER_EDIT, ...
├── Voter/
│   ├── PermissionVoter.php                # Role-based (checks PermissionMapProvider)
│   └── OwnerVoter.php                     # Subject-based (checks OwnableInterface)
├── OwnableInterface.php                   # Contract for entities with an owner
├── PermissionMapProvider.php              # Static mapping: permission → roles
└── DependencyInjection/
    └── PermissionEnumDiscoveryPass.php    # Auto-discovers permission enums
```

## How It Works

Two independent voters, combined with the `affirmative` strategy (one GRANT is enough):

- **PermissionVoter** checks if the user has a role listed in `PermissionMapProvider::$map` for the given permission.
- **OwnerVoter** checks if the user is the owner of the subject (only when the permission is ownable AND the subject implements `OwnableInterface`).

```
is_granted(ProjectPermissions::edit, $project)
│
├─ PermissionVoter: user has ROLE_ADMIN → GRANT     (role bypass)
├─ OwnerVoter: user is owner → GRANT                (ownership)
└─ Result: access granted if at least one GRANT
```

## Adding a New Domain

### 1. Create the enum

```php
// src/Security/Permissions/ProjectPermissions.php
enum ProjectPermissions: string implements PermissionInterface
{
    case view = 'PROJECT_VIEW';
    case create = 'PROJECT_CREATE';
    case edit = 'PROJECT_EDIT';
    case delete = 'PROJECT_DELETE';

    public function isOwnable(): bool
    {
        return match ($this) {
            self::edit, self::delete => true,
            default => false,
        };
    }
}
```

Naming convention: `DOMAIN_ACTION` (e.g. `PROJECT_EDIT`, `DOCUMENT_DELETE`).

The `CompilerPass` discovers the enum automatically. No registration needed.

### 2. Add entries to the permission map

```php
// src/Security/PermissionMapProvider.php
public static array $map = [
    // ...existing entries...
    ProjectPermissions::view->value   => ['ROLE_USER'],
    ProjectPermissions::create->value => ['ROLE_ADMIN'],
    ProjectPermissions::edit->value   => ['ROLE_ADMIN'],
    ProjectPermissions::delete->value => ['ROLE_SUPER_ADMIN'],
];
```

### 3. If ownership is needed, implement OwnableInterface

```php
class Project implements OwnableInterface
{
    public function getOwner(): ?UserInterface
    {
        return $this->createdBy;
    }
}
```

### 4. Use in controllers

```php
#[IsGranted(ProjectPermissions::view->value)]
final class ListProjectController extends AbstractController { ... }

#[IsGranted(ProjectPermissions::edit->value, subject: 'project')]
final class EditProjectController extends AbstractController
{
    public function __invoke(Project $project): Response { ... }
}
```

Note: `#[IsGranted]` requires `->value` (string), not the enum case directly.

For runtime checks: pass the enum case directly to `Security::isGranted()`.

```php
$this->security->isGranted(ProjectPermissions::edit, $project);
```

### 5. Use in Twig

```twig
{% if is_granted(enum('App\\Security\\Permissions\\ProjectPermissions').edit, project) %}
    <a href="{{ path('app_project_edit', {id: project.id}) }}">Edit</a>
{% endif %}
```

## Rules

- One permission per action. No `_OWN` suffixes. The same permission works with and without a subject.
- One voter = one responsibility. Never mix role checks and ownership checks.
- Always pass the subject when available: `is_granted(Permission::edit, $entity)`.
- `PermissionMapProvider` must have an entry for every permission enum case. The `PermissionMapProviderTest` enforces this.
- Do not use `ROLE_*` as enum values.

## Current Permission Map

| Permission | Roles |
|---|---|
| `USER_VIEW` | `ROLE_USER` |
| `USER_CREATE` | `ROLE_ADMIN` |
| `USER_EDIT` | `ROLE_ADMIN` |
| `USER_DELETE` | `ROLE_SUPER_ADMIN` |
| `USER_IMPERSONATE` | `ROLE_SUPER_ADMIN` |
