<?php

declare(strict_types=1);

namespace App\Tests\Shared\Content;

use App\Shared\Content\Block\HeadingBlock;
use App\Shared\Content\Block\HeadingLevel;
use App\Shared\Content\Block\ImageAlignment;
use App\Shared\Content\Block\ImageBlock;
use App\Shared\Content\Block\TextBlock;
use App\Shared\Content\BlockSerializer;
use PHPUnit\Framework\TestCase;

final class BlockSerializerTest extends TestCase
{
    public function testDeserializeDispatchesByType(): void
    {
        $serializer = new BlockSerializer();

        $blocks = $serializer->deserialize([
            ['type' => 'text', 'html' => '<p>Hello</p>'],
            ['type' => 'heading', 'text' => 'A title', 'level' => 'h3'],
            ['type' => 'image', 'path' => 'sample.jpg', 'alt' => 'Sample', 'alignment' => 'center'],
        ]);

        self::assertCount(3, $blocks);
        self::assertInstanceOf(TextBlock::class, $blocks[0]);
        self::assertSame('<p>Hello</p>', $blocks[0]->html);
        self::assertInstanceOf(HeadingBlock::class, $blocks[1]);
        self::assertSame('A title', $blocks[1]->text);
        self::assertSame(HeadingLevel::H3, $blocks[1]->level);
        self::assertInstanceOf(ImageBlock::class, $blocks[2]);
        self::assertSame('sample.jpg', $blocks[2]->path);
        self::assertSame(ImageAlignment::Center, $blocks[2]->alignment);
    }

    public function testDeserializeSkipsUnknownTypes(): void
    {
        $serializer = new BlockSerializer();

        $blocks = $serializer->deserialize([
            ['type' => 'text', 'html' => 'ok'],
            ['type' => 'unknown_type', 'data' => 'whatever'],
            ['type' => 'heading', 'text' => 'title'],
        ]);

        self::assertCount(2, $blocks);
    }

    public function testRoundtripPreservesData(): void
    {
        $serializer = new BlockSerializer();

        $original = [
            new TextBlock('<p>roundtrip</p>'),
            new HeadingBlock('Title', HeadingLevel::H4),
            new ImageBlock('img.png', 'alt', 'caption', ImageAlignment::Left),
        ];

        $deserialized = $serializer->deserialize($serializer->serialize($original));

        self::assertCount(3, $deserialized);
        self::assertEquals($original[0]->html, $deserialized[0]->html);
        self::assertEquals($original[1]->text, $deserialized[1]->text);
        self::assertEquals($original[1]->level, $deserialized[1]->level);
        self::assertEquals($original[2]->path, $deserialized[2]->path);
        self::assertEquals($original[2]->caption, $deserialized[2]->caption);
        self::assertEquals($original[2]->alignment, $deserialized[2]->alignment);
    }
}
