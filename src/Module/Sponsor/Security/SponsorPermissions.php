<?php

declare(strict_types=1);

namespace App\Module\Sponsor\Security;

use App\Shared\Security\Permissions\PermissionInterface;

enum SponsorPermissions: string implements PermissionInterface
{
    case view = 'SPONSOR_VIEW';
    case create = 'SPONSOR_CREATE';
    case edit = 'SPONSOR_EDIT';
    case delete = 'SPONSOR_DELETE';

    public function isOwnable(): bool
    {
        return false;
    }
}
