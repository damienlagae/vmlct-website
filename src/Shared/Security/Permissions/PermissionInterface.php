<?php

declare(strict_types=1);

namespace App\Shared\Security\Permissions;

/**
 * Contract every domain permission enum must implement.
 *
 * Implementing classes are backed enums whose case values are strings of the
 * form DOMAIN_ACTION (e.g. SPONSOR_EDIT, ARTICLE_DELETE). Voters and the
 * PermissionMapProvider consume these values; ownership-aware permissions
 * declare it via isOwnable().
 */
interface PermissionInterface extends \BackedEnum
{
    public function isOwnable(): bool;
}
