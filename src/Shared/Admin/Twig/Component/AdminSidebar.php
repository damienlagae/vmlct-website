<?php

declare(strict_types=1);

namespace App\Shared\Admin\Twig\Component;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

/**
 * @phpstan-type SidebarChild array{label: string, route: string, icon: string, match: list<string>}
 * @phpstan-type SidebarItem array{label: string, route: string, icon: string, match: list<string>, children?: list<SidebarChild>}
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
            ['label' => 'admin.sidebar.news', 'route' => 'admin_article_index', 'icon' => 'bi:newspaper', 'match' => ['admin_article_index', 'admin_article_create', 'admin_article_edit']],
            ['label' => 'admin.sidebar.pages', 'route' => 'admin_page_index', 'icon' => 'bi:file-earmark-text', 'match' => ['admin_page_index', 'admin_page_create', 'admin_page_edit']],
            [
                'label' => 'admin.sidebar.team',
                'route' => 'admin_team_dashboard',
                'icon' => 'bi:bicycle',
                'match' => ['admin_team_dashboard', 'admin_rider_index', 'admin_rider_create', 'admin_rider_edit', 'admin_staff_index', 'admin_staff_create', 'admin_staff_edit'],
                'children' => [
                    ['label' => 'admin.sidebar.staff', 'route' => 'admin_staff_index', 'icon' => 'bi:person-badge', 'match' => ['admin_staff_index', 'admin_staff_create', 'admin_staff_edit']],
                    ['label' => 'admin.sidebar.riders', 'route' => 'admin_rider_index', 'icon' => 'bi:bicycle', 'match' => ['admin_rider_index', 'admin_rider_create', 'admin_rider_edit']],
                ],
            ],
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
