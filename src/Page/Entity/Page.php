<?php

declare(strict_types=1);

namespace App\Page\Entity;

use App\Page\Repository\PageRepository;
use App\Shared\Entity\HasUlidIdInterface;
use App\Shared\Entity\TimestampableInterface;
use App\Shared\Entity\TimestampableTrait;
use App\Shared\Entity\UlidIdTrait;
use DH\Auditor\Attribute\Auditable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Ulid;

/**
 * Dynamic, block-based page resolved by a catch-all controller against the
 * editable `path`. Storage of `content` follows the same dict-by-ULID shape
 * as Article so the auditor diffs by block id and not by index.
 */
#[ORM\Entity(repositoryClass: PageRepository::class)]
#[Auditable]
class Page implements HasUlidIdInterface, TimestampableInterface
{
    use UlidIdTrait;
    use TimestampableTrait;

    #[ORM\Column(length: 200)]
    private string $title;

    #[ORM\Column(length: 220, unique: true)]
    private string $path;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $excerpt = null;

    /**
     * @var array<string, array<string, mixed>>
     */
    #[ORM\Column(type: Types::JSON)]
    private array $content = [];

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $publishedAt = null;

    #[ORM\Column(length: 200, nullable: true)]
    private ?string $metaTitle = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $metaDescription = null;

    public function __construct(string $title, string $path)
    {
        $this->title = $title;
        $this->path = $path;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function setPath(string $path): void
    {
        $this->path = $path;
    }

    public function getExcerpt(): ?string
    {
        return $this->excerpt;
    }

    public function setExcerpt(?string $excerpt): void
    {
        $this->excerpt = $excerpt;
    }

    /**
     * Ordered list of blocks (sorted by `position`), each payload carries its `id`.
     *
     * @return list<array<string, mixed>>
     */
    public function getContent(): array
    {
        if ([] === $this->content) {
            return [];
        }

        $blocks = [];
        foreach ($this->content as $key => $payload) {
            if (!\is_array($payload)) {
                continue;
            }
            if (\is_int($key)) {
                $payload['position'] ??= $key;
            } else {
                $payload['id'] = (string) $key;
            }
            $blocks[] = $payload;
        }

        usort(
            $blocks,
            static fn (array $a, array $b): int => ($a['position'] ?? 0) <=> ($b['position'] ?? 0),
        );

        return $blocks;
    }

    /**
     * @param list<array<string, mixed>> $blocks
     */
    public function setContent(array $blocks): void
    {
        $dict = [];
        foreach (array_values($blocks) as $position => $payload) {
            if (!\is_array($payload)) {
                continue;
            }
            $id = $payload['id'] ?? null;
            if (!\is_string($id) || '' === $id) {
                $id = (string) new Ulid();
            }
            unset($payload['id']);
            $payload['position'] = $position;
            $dict[$id] = $payload;
        }

        $this->content = $dict;
    }

    public function getPublishedAt(): ?\DateTimeImmutable
    {
        return $this->publishedAt;
    }

    public function setPublishedAt(?\DateTimeImmutable $publishedAt): void
    {
        $this->publishedAt = $publishedAt;
    }

    public function isPublished(): bool
    {
        return null !== $this->publishedAt && $this->publishedAt <= new \DateTimeImmutable();
    }

    public function getMetaTitle(): ?string
    {
        return $this->metaTitle;
    }

    public function setMetaTitle(?string $metaTitle): void
    {
        $this->metaTitle = $metaTitle;
    }

    public function getMetaDescription(): ?string
    {
        return $this->metaDescription;
    }

    public function setMetaDescription(?string $metaDescription): void
    {
        $this->metaDescription = $metaDescription;
    }
}
