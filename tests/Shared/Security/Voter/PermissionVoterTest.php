<?php

declare(strict_types=1);

namespace App\Tests\Shared\Security\Voter;

use App\Module\Sponsor\Security\SponsorPermissions;
use App\Shared\Security\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

final class PermissionVoterTest extends KernelTestCase
{
    use Factories;
    use ResetDatabase;

    public function testRoleUserCanViewSponsorButCannotCreate(): void
    {
        self::bootKernel();
        $manager = self::getContainer()->get(AccessDecisionManagerInterface::class);
        \assert($manager instanceof AccessDecisionManagerInterface);

        $user = UserFactory::createOne();
        $token = new UsernamePasswordToken($user, 'main', $user->getRoles());

        self::assertTrue($manager->decide($token, [SponsorPermissions::view->value]));
        self::assertFalse($manager->decide($token, [SponsorPermissions::create->value]));
        self::assertFalse($manager->decide($token, [SponsorPermissions::delete->value]));
    }

    public function testRoleAdminCanCreateAndEditButCannotDelete(): void
    {
        self::bootKernel();
        $manager = self::getContainer()->get(AccessDecisionManagerInterface::class);
        \assert($manager instanceof AccessDecisionManagerInterface);

        $admin = UserFactory::new()->admin()->create();
        $token = new UsernamePasswordToken($admin, 'main', $admin->getRoles());

        self::assertTrue($manager->decide($token, [SponsorPermissions::view->value]));
        self::assertTrue($manager->decide($token, [SponsorPermissions::create->value]));
        self::assertTrue($manager->decide($token, [SponsorPermissions::edit->value]));
        self::assertFalse($manager->decide($token, [SponsorPermissions::delete->value]));
    }

    public function testRoleSuperAdminCanDoEverything(): void
    {
        self::bootKernel();
        $manager = self::getContainer()->get(AccessDecisionManagerInterface::class);
        \assert($manager instanceof AccessDecisionManagerInterface);

        $superAdmin = UserFactory::new()->superAdmin()->create();
        $token = new UsernamePasswordToken($superAdmin, 'main', $superAdmin->getRoles());

        foreach (SponsorPermissions::cases() as $case) {
            self::assertTrue(
                $manager->decide($token, [$case->value]),
                sprintf('Super admin should be granted %s', $case->value),
            );
        }
    }
}
