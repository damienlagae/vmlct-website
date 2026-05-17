<?php

declare(strict_types=1);

namespace App\Module\News\Security;

use App\Shared\Security\Permissions\PermissionInterface;

enum NewsPermissions: string implements PermissionInterface
{
    case view = 'NEWS_VIEW';
    case create = 'NEWS_CREATE';
    case edit = 'NEWS_EDIT';
    case delete = 'NEWS_DELETE';

    public function isOwnable(): bool
    {
        return false;
    }
}
