<?php

declare(strict_types=1);

namespace App\Module\Menu\Routing;

/**
 * Whitelist of Symfony route names that the menu admin can target.
 *
 * Kept as a plain constant rather than scanning the router so we can
 * decide explicitly which routes are nav-eligible (no admin/_profiler/
 * audit routes leaking into the select). Add a label translation key
 * alongside the route name; the admin form uses it for the dropdown.
 */
final class MenuRouteCatalog
{
    /**
     * Map of route name => translation key for the label shown in the
     * admin dropdown. Routes must already be registered in the app.
     *
     * @var array<string, string>
     */
    public const ROUTES = [
        'home' => 'menu.target.route.home',
        'news_index' => 'menu.target.route.news',
        'team_index' => 'menu.target.route.team',
    ];

    /**
     * @return array<string, string>
     */
    public static function choices(): array
    {
        return array_flip(self::ROUTES); // label_key => route_name (for ChoiceType)
    }

    public static function knows(string $routeName): bool
    {
        return isset(self::ROUTES[$routeName]);
    }
}
