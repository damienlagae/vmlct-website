<?php

declare(strict_types=1);

namespace App\Module\Menu\Factory;

use App\Module\Menu\Entity\MenuItem;
use App\Module\Menu\Entity\MenuTargetType;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<MenuItem>
 */
final class MenuItemFactory extends PersistentObjectFactory
{
    public static function class(): string
    {
        return MenuItem::class;
    }

    /**
     * @return array<string, mixed>
     */
    protected function defaults(): array
    {
        return [
            'label' => self::faker()->unique()->words(2, true),
            'targetType' => MenuTargetType::Route,
            'routeName' => 'home',
            'position' => 0,
            'active' => true,
            'openInNewTab' => false,
        ];
    }
}
