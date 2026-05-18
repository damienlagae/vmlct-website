<?php

declare(strict_types=1);

namespace App\Module\Menu\Entity;

enum MenuTargetType: string
{
    case Page = 'page';
    case Route = 'route';
}
