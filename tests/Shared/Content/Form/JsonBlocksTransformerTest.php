<?php

declare(strict_types=1);

namespace App\Tests\Shared\Content\Form;

use App\Shared\Content\Form\JsonBlocksTransformer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Exception\TransformationFailedException;

final class JsonBlocksTransformerTest extends TestCase
{
    public function testTransformNullReturnsEmptyArrayJson(): void
    {
        self::assertSame('[]', (new JsonBlocksTransformer())->transform(null));
    }

    public function testTransformEncodesArrayWithUnescapedSlashesAndUnicode(): void
    {
        $payload = [
            ['type' => 'text', 'html' => '<a href="https://vmlct.local">Übermorgen</a>'],
        ];

        $json = (new JsonBlocksTransformer())->transform($payload);

        self::assertStringContainsString('https://vmlct.local', $json);
        self::assertStringNotContainsString('\/', $json);
        self::assertStringContainsString('Übermorgen', $json);
    }

    public function testTransformRejectsNonArray(): void
    {
        $this->expectException(TransformationFailedException::class);

        (new JsonBlocksTransformer())->transform('not-an-array');
    }

    public function testReverseTransformReturnsListOfArrays(): void
    {
        $blocks = (new JsonBlocksTransformer())->reverseTransform('[{"type":"text","html":"<p>hi</p>"},{"type":"heading","text":"t","level":"h2"}]');

        self::assertCount(2, $blocks);
        self::assertSame('text', $blocks[0]['type']);
        self::assertSame('heading', $blocks[1]['type']);
    }

    public function testReverseTransformEmptyStringYieldsEmptyArray(): void
    {
        self::assertSame([], (new JsonBlocksTransformer())->reverseTransform(''));
    }

    public function testReverseTransformRejectsInvalidJson(): void
    {
        $this->expectException(TransformationFailedException::class);

        (new JsonBlocksTransformer())->reverseTransform('{not json}');
    }

    public function testReverseTransformDropsScalarEntries(): void
    {
        $blocks = (new JsonBlocksTransformer())->reverseTransform('[{"type":"text"},"bogus",42,{"type":"heading"}]');

        self::assertCount(2, $blocks);
        self::assertSame('text', $blocks[0]['type']);
        self::assertSame('heading', $blocks[1]['type']);
    }
}
