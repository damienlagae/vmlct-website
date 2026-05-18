<?php

declare(strict_types=1);

namespace App\Module\Programme\Security;

use App\Shared\Security\Permissions\PermissionInterface;

enum ProgrammePermissions: string implements PermissionInterface
{
    case view = 'PROGRAMME_VIEW';
    case create = 'PROGRAMME_CREATE';
    case edit = 'PROGRAMME_EDIT';
    case delete = 'PROGRAMME_DELETE';

    public function isOwnable(): bool
    {
        return false;
    }
}
