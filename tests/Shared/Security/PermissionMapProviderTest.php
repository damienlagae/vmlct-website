<?php

declare(strict_types=1);

namespace App\Tests\Shared\Security;

use App\Shared\Security\PermissionMapProvider;
use App\Shared\Security\PermissionRegistry;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class PermissionMapProviderTest extends KernelTestCase
{
    public function testEveryDiscoveredPermissionCaseHasAMapEntry(): void
    {
        self::bootKernel();
        $registry = self::getContainer()->get(PermissionRegistry::class);
        \assert($registry instanceof PermissionRegistry);

        $missing = [];
        foreach ($registry->all() as $case) {
            if (!PermissionMapProvider::knows($case->value)) {
                $missing[] = $case::class.'::'.$case->name.' ('.$case->value.')';
            }
        }

        self::assertSame([], $missing, 'These permission cases are missing from PermissionMapProvider::$map: '.implode(', ', $missing));
    }

    public function testDiscoveryFoundAtLeastOnePermissionEnum(): void
    {
        self::bootKernel();
        $registry = self::getContainer()->get(PermissionRegistry::class);
        \assert($registry instanceof PermissionRegistry);

        self::assertNotEmpty($registry->getEnumClasses(), 'PermissionEnumDiscoveryPass should find at least one *Permissions enum');
    }
}
