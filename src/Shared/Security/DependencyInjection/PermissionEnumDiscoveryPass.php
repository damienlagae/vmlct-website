<?php

declare(strict_types=1);

namespace App\Shared\Security\DependencyInjection;

use App\Shared\Security\PermissionRegistry;
use App\Shared\Security\Permissions\PermissionInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Finder\Finder;

/**
 * Scans src/ for enums implementing PermissionInterface and injects the
 * full list into the PermissionRegistry service. This is what allows
 * modules to define their own permission enums without registering
 * anything by hand.
 */
final class PermissionEnumDiscoveryPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition(PermissionRegistry::class)) {
            return;
        }

        $projectDir = $container->getParameter('kernel.project_dir');
        \assert(\is_string($projectDir));

        $srcDir = $projectDir.'/src';
        if (!is_dir($srcDir)) {
            return;
        }

        $finder = new Finder();
        $finder->files()->in($srcDir)->name('*Permissions.php');

        $enumClasses = [];
        foreach ($finder as $file) {
            $relative = $file->getRelativePathname();
            $class = 'App\\'.str_replace(['/', '.php'], ['\\', ''], $relative);

            if (!enum_exists($class)) {
                continue;
            }

            $reflection = new \ReflectionEnum($class);
            if (!$reflection->implementsInterface(PermissionInterface::class)) {
                continue;
            }

            $enumClasses[] = $class;
        }

        sort($enumClasses);

        $container->getDefinition(PermissionRegistry::class)
            ->setArgument('$enumClasses', $enumClasses);
    }
}
