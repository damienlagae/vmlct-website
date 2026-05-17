<?php

declare(strict_types=1);

namespace App\Shared\Security\Permissions;

enum UserPermissions: string implements PermissionInterface
{
    case view = 'USER_VIEW';
    case create = 'USER_CREATE';
    case edit = 'USER_EDIT';
    case delete = 'USER_DELETE';
    case impersonate = 'USER_IMPERSONATE';

    public function isOwnable(): bool
    {
        return false;
    }
}
