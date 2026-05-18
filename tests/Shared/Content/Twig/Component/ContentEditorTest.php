<?php

declare(strict_types=1);

namespace App\Tests\Shared\Content\Twig\Component;

use App\Shared\Content\Twig\Component\ContentEditor;
use PHPUnit\Framework\TestCase;

final class ContentEditorTest extends TestCase
{
    public function testAppendBlockAddsATextBlockTemplate(): void
    {
        $editor = new ContentEditor();
        $editor->appendBlock('text');

        self::assertCount(1, $editor->blocks);
        self::assertSame('text', $editor->blocks[0]['type']);
        self::assertSame('', $editor->blocks[0]['html']);
        self::assertNotEmpty($editor->blocks[0]['id']);
    }

    public function testAppendBlockAddsAHeadingBlockTemplate(): void
    {
        $editor = new ContentEditor();
        $editor->appendBlock('heading');

        self::assertSame('heading', $editor->blocks[0]['type']);
        self::assertSame('', $editor->blocks[0]['text']);
        self::assertSame('h2', $editor->blocks[0]['level']);
        self::assertNotEmpty($editor->blocks[0]['id']);
    }

    public function testAppendBlockAddsAnImageBlockTemplate(): void
    {
        $editor = new ContentEditor();
        $editor->appendBlock('image');

        $block = $editor->blocks[0];
        self::assertSame('image', $block['type']);
        self::assertSame('', $block['path']);
        self::assertSame('', $block['alt']);
        self::assertSame('', $block['caption']);
        self::assertSame('center', $block['alignment']);
        self::assertNotEmpty($block['id']);
    }

    public function testAppendBlockEachInsertionReceivesADistinctId(): void
    {
        $editor = new ContentEditor();
        $editor->appendBlock('text');
        $editor->appendBlock('text');

        self::assertNotSame($editor->blocks[0]['id'], $editor->blocks[1]['id']);
    }

    public function testAppendBlockIgnoresUnknownType(): void
    {
        $editor = new ContentEditor();
        $editor->appendBlock('nope');

        self::assertSame([], $editor->blocks);
    }

    public function testDeleteBlockRemovesAndReindexes(): void
    {
        $editor = new ContentEditor();
        $editor->blocks = [
            ['type' => 'text', 'html' => 'a'],
            ['type' => 'text', 'html' => 'b'],
            ['type' => 'text', 'html' => 'c'],
        ];

        $editor->deleteBlock(1);

        self::assertCount(2, $editor->blocks);
        self::assertSame('a', $editor->blocks[0]['html']);
        self::assertSame('c', $editor->blocks[1]['html']);
    }

    public function testMoveUpSwapsWithPrevious(): void
    {
        $editor = new ContentEditor();
        $editor->blocks = [
            ['type' => 'text', 'html' => 'first'],
            ['type' => 'text', 'html' => 'second'],
        ];

        $editor->moveUp(1);

        self::assertSame('second', $editor->blocks[0]['html']);
        self::assertSame('first', $editor->blocks[1]['html']);
    }

    public function testMoveUpDoesNothingAtTop(): void
    {
        $editor = new ContentEditor();
        $editor->blocks = [
            ['type' => 'text', 'html' => 'first'],
            ['type' => 'text', 'html' => 'second'],
        ];

        $editor->moveUp(0);

        self::assertSame('first', $editor->blocks[0]['html']);
    }

    public function testMoveDownSwapsWithNext(): void
    {
        $editor = new ContentEditor();
        $editor->blocks = [
            ['type' => 'text', 'html' => 'first'],
            ['type' => 'text', 'html' => 'second'],
        ];

        $editor->moveDown(0);

        self::assertSame('second', $editor->blocks[0]['html']);
        self::assertSame('first', $editor->blocks[1]['html']);
    }

    public function testMoveDownDoesNothingAtBottom(): void
    {
        $editor = new ContentEditor();
        $editor->blocks = [
            ['type' => 'text', 'html' => 'first'],
        ];

        $editor->moveDown(0);

        self::assertSame('first', $editor->blocks[0]['html']);
    }
}
