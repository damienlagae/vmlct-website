<?php

declare(strict_types=1);

namespace App\Module\Team\Security;

use App\Shared\Security\Permissions\PermissionInterface;

enum TeamPermissions: string implements PermissionInterface
{
    case view = 'TEAM_VIEW';
    case create = 'TEAM_CREATE';
    case edit = 'TEAM_EDIT';
    case delete = 'TEAM_DELETE';

    public function isOwnable(): bool
    {
        return false;
    }
}
