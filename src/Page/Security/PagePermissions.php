<?php

declare(strict_types=1);

namespace App\Page\Security;

use App\Shared\Security\Permissions\PermissionInterface;

enum PagePermissions: string implements PermissionInterface
{
    case view = 'PAGE_VIEW';
    case create = 'PAGE_CREATE';
    case edit = 'PAGE_EDIT';
    case delete = 'PAGE_DELETE';

    public function isOwnable(): bool
    {
        return false;
    }
}
