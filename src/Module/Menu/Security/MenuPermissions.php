<?php

declare(strict_types=1);

namespace App\Module\Menu\Security;

use App\Shared\Security\Permissions\PermissionInterface;

enum MenuPermissions: string implements PermissionInterface
{
    case view = 'MENU_VIEW';
    case create = 'MENU_CREATE';
    case edit = 'MENU_EDIT';
    case delete = 'MENU_DELETE';

    public function isOwnable(): bool
    {
        return false;
    }
}
