<?php

declare(strict_types=1);

namespace App\Shared\Admin\Twig\Component;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

/**
 * @phpstan-type SidebarItem array{label: string, route: string, icon: string, match: list<string>}
 */
#[AsTwigComponent(name: 'Admin:Sidebar', template: '@Shared/admin/components/Sidebar.html.twig')]
final class AdminSidebar
{
    public function __construct(
        private readonly RequestStack $requestStack,
        private readonly RouterInterface $router,
    ) {
    }

    /**
     * @return list<SidebarItem>
     */
    public function getItems(): array
    {
        return [
            ['label' => 'admin.sidebar.dashboard', 'route' => 'admin_dashboard', 'icon' => 'bi:speedometer2', 'match' => ['admin_dashboard']],
            ['label' => 'admin.sidebar.team', 'route' => 'admin_team_dashboard', 'icon' => 'bi:bicycle', 'match' => ['admin_team_dashboard', 'admin_rider_index', 'admin_rider_create', 'admin_rider_edit', 'admin_staff_index', 'admin_staff_create', 'admin_staff_edit']],
            ['label' => 'admin.sidebar.sponsors', 'route' => 'admin_sponsor_index', 'icon' => 'bi:award', 'match' => ['admin_sponsor_index', 'admin_sponsor_create', 'admin_sponsor_edit']],
            ['label' => 'admin.sidebar.users', 'route' => 'admin_user_index', 'icon' => 'bi:people', 'match' => ['admin_user_index', 'admin_user_create', 'admin_user_edit']],
        ];
    }

    public function isActive(string ...$routeNames): bool
    {
        $currentRoute = $this->requestStack->getCurrentRequest()?->attributes->get('_route');

        return \is_string($currentRoute) && \in_array($currentRoute, $routeNames, true);
    }

    public function routeExists(string $routeName): bool
    {
        return null !== $this->router->getRouteCollection()->get($routeName);
    }
}
