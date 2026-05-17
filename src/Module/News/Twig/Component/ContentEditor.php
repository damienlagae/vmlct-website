<?php

declare(strict_types=1);

namespace App\Module\News\Twig\Component;

use Symfony\Component\Uid\Ulid;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent(name: 'News:ContentEditor', template: '@News/admin/components/ContentEditor.html.twig')]
final class ContentEditor
{
    use DefaultActionTrait;

    /**
     * Block payloads carried as plain arrays so they can be JSON-serialised
     * for the hidden form input that feeds the surrounding ArticleType form.
     *
     * @var list<array<string, mixed>>
     */
    #[LiveProp(writable: true)]
    public array $blocks = [];

    /** Full form-field name to use for the hidden input (e.g. "article[content]"). */
    #[LiveProp]
    public string $fieldName = '';

    #[LiveAction]
    public function appendBlock(#[LiveArg] string $type): void
    {
        $template = match ($type) {
            'text' => ['type' => 'text', 'html' => ''],
            'heading' => ['type' => 'heading', 'text' => '', 'level' => 'h2'],
            'image' => ['type' => 'image', 'path' => '', 'alt' => '', 'caption' => '', 'alignment' => 'center'],
            default => null,
        };

        if (null !== $template) {
            $template['id'] = (string) new Ulid();
            $this->blocks[] = $template;
        }
    }

    #[LiveAction]
    public function deleteBlock(#[LiveArg] int $index): void
    {
        if (!isset($this->blocks[$index])) {
            return;
        }

        $next = $this->blocks;
        unset($next[$index]);
        $this->blocks = array_values($next);
    }

    #[LiveAction]
    public function moveUp(#[LiveArg] int $index): void
    {
        if ($index <= 0 || !isset($this->blocks[$index])) {
            return;
        }

        [$this->blocks[$index - 1], $this->blocks[$index]] = [$this->blocks[$index], $this->blocks[$index - 1]];
    }

    #[LiveAction]
    public function moveDown(#[LiveArg] int $index): void
    {
        if (!isset($this->blocks[$index], $this->blocks[$index + 1])) {
            return;
        }

        [$this->blocks[$index], $this->blocks[$index + 1]] = [$this->blocks[$index + 1], $this->blocks[$index]];
    }
}
