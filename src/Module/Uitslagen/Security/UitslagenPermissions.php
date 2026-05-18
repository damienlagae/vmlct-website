<?php

declare(strict_types=1);

namespace App\Module\Uitslagen\Security;

use App\Shared\Security\Permissions\PermissionInterface;

enum UitslagenPermissions: string implements PermissionInterface
{
    case view = 'UITSLAGEN_VIEW';
    case create = 'UITSLAGEN_CREATE';
    case edit = 'UITSLAGEN_EDIT';
    case delete = 'UITSLAGEN_DELETE';

    public function isOwnable(): bool
    {
        return false;
    }
}
