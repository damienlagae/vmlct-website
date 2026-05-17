<?php

declare(strict_types=1);

namespace App\Tests\Shared\Content\Block;

use App\Shared\Content\Block\TextBlock;
use PHPUnit\Framework\TestCase;

final class TextBlockTest extends TestCase
{
    public function testRenderedHtmlDowngradesH1ToH2(): void
    {
        $block = new TextBlock('<h1>Title</h1><p>body</p>');

        self::assertSame('<h2>Title</h2><p>body</p>', $block->renderedHtml());
    }

    public function testRenderedHtmlPreservesH1Attributes(): void
    {
        $block = new TextBlock('<h1 id="x" class="lead">Title</h1>');

        self::assertSame('<h2 id="x" class="lead">Title</h2>', $block->renderedHtml());
    }

    public function testRenderedHtmlIsAStraightPassthroughWhenNoH1IsPresent(): void
    {
        $block = new TextBlock('<p>Just <strong>text</strong>.</p><h2>Sub</h2>');

        self::assertSame('<p>Just <strong>text</strong>.</p><h2>Sub</h2>', $block->renderedHtml());
    }
}
