<?php

declare(strict_types=1);

namespace App\Module\News\Entity;

use App\Module\News\Repository\ArticleRepository;
use App\Shared\Entity\HasUlidIdInterface;
use App\Shared\Entity\TimestampableInterface;
use App\Shared\Entity\TimestampableTrait;
use App\Shared\Entity\UlidIdTrait;
use App\Shared\Security\Entity\User;
use App\Shared\Seo\Entity\SeoableInterface;
use App\Shared\Seo\Entity\SeoableTrait;
use DH\Auditor\Attribute\Auditable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Uid\Ulid;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: ArticleRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[Vich\Uploadable]
#[Auditable]
class Article implements HasUlidIdInterface, SeoableInterface, TimestampableInterface
{
    use SeoableTrait;
    use TimestampableTrait;
    use UlidIdTrait;

    #[ORM\Column(length: 200)]
    private string $title;

    #[ORM\Column(length: 220, unique: true)]
    private string $slug;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $excerpt = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $coverPath = null;

    #[Vich\UploadableField(mapping: 'article_cover', fileNameProperty: 'coverPath')]
    private ?File $coverFile = null;

    /**
     * Internal storage shape: dict `<blockId, {position, ...payload}>`.
     * Stored as a JSON object so that auditor diffs by block id (stable) and
     * not by index (cascades on insertion/removal). Always normalised through
     * `setContent()` — never assigned directly from the outside.
     *
     * @var array<string, array<string, mixed>>
     */
    #[ORM\Column(type: Types::JSON)]
    private array $content = [];

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $publishedAt = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?User $author = null;

    public function __construct(string $title, string $slug)
    {
        $this->title = $title;
        $this->slug = $slug;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): void
    {
        $this->slug = $slug;
    }

    public function getExcerpt(): ?string
    {
        return $this->excerpt;
    }

    public function setExcerpt(?string $excerpt): void
    {
        $this->excerpt = $excerpt;
    }

    public function getCoverPath(): ?string
    {
        return $this->coverPath;
    }

    public function setCoverPath(?string $coverPath): void
    {
        $this->coverPath = $coverPath;
    }

    public function getCoverFile(): ?File
    {
        return $this->coverFile;
    }

    public function setCoverFile(?File $coverFile): void
    {
        $this->coverFile = $coverFile;

        if (null !== $coverFile) {
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    /**
     * Ordered list of blocks, sorted by their persisted `position`. Each
     * payload carries its `id` (string) so the editor / consumers can match
     * blocks across saves. Legacy list-shaped content is tolerated and gets
     * a fresh ULID on the next `setContent()` round-trip.
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
                // Legacy list-shape — synthesise position from index, leave id
                // unset so the next save assigns a stable ULID.
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
     * Accepts an ordered list of block payloads (each may carry an `id`).
     * Normalises into the dict storage shape: stable ULID as key, sequential
     * `position` reflecting the list order.
     *
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

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): void
    {
        $this->author = $author;
    }
}
