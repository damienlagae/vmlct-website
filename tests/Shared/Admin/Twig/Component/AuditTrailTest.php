<?php

declare(strict_types=1);

namespace App\Tests\Shared\Admin\Twig\Component;

use App\Shared\Admin\Twig\Component\AuditTrail;
use PHPUnit\Framework\TestCase;

final class AuditTrailTest extends TestCase
{
    public function testClassifyChangeDetectsAdditionWhenAllSubFieldsCarryOnlyNew(): void
    {
        $op = AuditTrail::classifyChange([
            'type' => ['new' => 'text'],
            'html' => ['new' => '<p>hi</p>'],
        ]);

        self::assertSame('add', $op);
    }

    public function testClassifyChangeDetectsRemovalWhenAllSubFieldsCarryOnlyOld(): void
    {
        $op = AuditTrail::classifyChange([
            'type' => ['old' => 'heading'],
            'text' => ['old' => 'gone'],
            'level' => ['old' => 'h2'],
        ]);

        self::assertSame('remove', $op);
    }

    public function testClassifyChangeReturnsModifyForMixedSubFields(): void
    {
        $op = AuditTrail::classifyChange([
            'type' => ['old' => 'text', 'new' => 'heading'],
            'html' => ['old' => '<p>x</p>'],
            'text' => ['new' => 'subtitle'],
        ]);

        self::assertSame('modify', $op);
    }

    public function testClassifyChangeIgnoresScalarKeys(): void
    {
        $op = AuditTrail::classifyChange([
            'type' => ['new' => 'text'],
            'noise' => 'not-a-change-shape',
        ]);

        self::assertSame('add', $op);
    }
}
