<?php

declare(strict_types=1);

namespace App\Tests\Shared\Entity;

use App\Shared\Entity\TimestampableInterface;
use App\Shared\Entity\TimestampableTrait;
use PHPUnit\Framework\TestCase;

final class TimestampableTraitTest extends TestCase
{
    public function testInitTimestampsSetsCreatedAndUpdatedToTheSameInstant(): void
    {
        $entity = new class () implements TimestampableInterface {
            use TimestampableTrait;
        };

        self::assertNull($entity->getCreatedAt());
        self::assertNull($entity->getUpdatedAt());

        $entity->initTimestamps();

        self::assertInstanceOf(\DateTimeImmutable::class, $entity->getCreatedAt());
        self::assertInstanceOf(\DateTimeImmutable::class, $entity->getUpdatedAt());
        self::assertEquals($entity->getCreatedAt(), $entity->getUpdatedAt());
    }

    public function testRefreshUpdatedAtMovesOnlyUpdatedAt(): void
    {
        $entity = new class () implements TimestampableInterface {
            use TimestampableTrait;
        };

        $entity->initTimestamps();
        $createdAt = $entity->getCreatedAt();

        usleep(1500);
        $entity->refreshUpdatedAt();

        self::assertSame($createdAt, $entity->getCreatedAt());
        self::assertGreaterThan($createdAt, $entity->getUpdatedAt());
    }
}
