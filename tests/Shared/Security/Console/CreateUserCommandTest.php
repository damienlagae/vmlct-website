<?php

declare(strict_types=1);

namespace App\Tests\Shared\Security\Console;

use App\Shared\Security\Factory\UserFactory;
use App\Shared\Security\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

final class CreateUserCommandTest extends KernelTestCase
{
    use Factories;
    use ResetDatabase;

    private function tester(): CommandTester
    {
        $kernel = self::bootKernel();
        $application = new Application($kernel);
        $command = $application->find('app:user:create');

        return new CommandTester($command);
    }

    public function testCreatesUserWithGivenRoleAndPassword(): void
    {
        $tester = $this->tester();

        $tester->execute([
            'email' => 'new@example.com',
            'password' => 's3cret',
            '--role' => 'ROLE_ADMIN',
            '--first-name' => 'New',
            '--last-name' => 'Person',
        ]);

        self::assertSame(Command::SUCCESS, $tester->getStatusCode());
        self::assertStringContainsString('created with role ROLE_ADMIN', $tester->getDisplay());

        $user = self::getContainer()->get(UserRepository::class)->findOneByEmail('new@example.com');
        self::assertNotNull($user);
        self::assertSame(['ROLE_ADMIN', 'ROLE_USER'], $user->getRoles());
        self::assertSame('New', $user->getFirstName());
        self::assertSame('Person', $user->getLastName());

        $hasher = self::getContainer()->get(UserPasswordHasherInterface::class);
        self::assertTrue($hasher->isPasswordValid($user, 's3cret'));
    }

    public function testFailsWhenUserAlreadyExists(): void
    {
        UserFactory::createOne(['email' => 'taken@example.com']);

        $tester = $this->tester();
        $tester->execute([
            'email' => 'taken@example.com',
            'password' => 'pw',
        ]);

        self::assertSame(Command::FAILURE, $tester->getStatusCode());
        self::assertStringContainsString('already exists', $tester->getDisplay());
    }

    public function testRejectsUnknownRole(): void
    {
        $tester = $this->tester();
        $tester->execute([
            'email' => 'rolecheck@example.com',
            'password' => 'pw',
            '--role' => 'ROLE_HACKER',
        ]);

        self::assertSame(Command::INVALID, $tester->getStatusCode());
        self::assertStringContainsString('Unknown role', $tester->getDisplay());
    }
}
