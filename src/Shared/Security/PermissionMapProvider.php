<?php

declare(strict_types=1);

namespace App\Shared\Security;

use App\Module\Sponsor\Security\SponsorPermissions;
use App\Shared\Security\Permissions\UserPermissions;

/**
 * Static mapping permission_value => list of roles allowed to perform it.
 *
 * Every permission case discovered via PermissionEnumDiscoveryPass must
 * have an entry here. PermissionMapProviderTest enforces this invariant.
 *
 * Role hierarchy applies: listing ROLE_ADMIN automatically grants
 * ROLE_SUPER_ADMIN as well (resolved via Symfony's RoleHierarchyInterface
 * inside PermissionVoter).
 */
final class PermissionMapProvider
{
    /**
     * @var array<string, list<string>>
     */
    public static array $map = [
        // SPONSOR
        SponsorPermissions::view->value => ['ROLE_USER'],
        SponsorPermissions::create->value => ['ROLE_ADMIN'],
        SponsorPermissions::edit->value => ['ROLE_ADMIN'],
        SponsorPermissions::delete->value => ['ROLE_SUPER_ADMIN'],

        // USER
        UserPermissions::view->value => ['ROLE_ADMIN'],
        UserPermissions::create->value => ['ROLE_ADMIN'],
        UserPermissions::edit->value => ['ROLE_ADMIN'],
        UserPermissions::delete->value => ['ROLE_SUPER_ADMIN'],
        UserPermissions::impersonate->value => ['ROLE_SUPER_ADMIN'],
    ];

    /**
     * @return list<string>
     */
    public static function getRoles(string $permission): array
    {
        return self::$map[$permission] ?? [];
    }

    public static function knows(string $permission): bool
    {
        return isset(self::$map[$permission]);
    }
}
