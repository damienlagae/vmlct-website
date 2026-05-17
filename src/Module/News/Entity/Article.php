<?php

declare(strict_types=1);

namespace App\Module\News\Entity;

use App\Module\News\Repository\ArticleRepository;
use App\Shared\Entity\HasUlidIdInterface;
use App\Shared\Entity\TimestampableInterface;
use App\Shared\Entity\TimestampableTrait;
use App\Shared\Entity\UlidIdTrait;
use App\Shared\Security\Entity\User;
use DH\Auditor\Attribute\Auditable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: ArticleRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[Vich\Uploadable]
#[Auditable]
class Article implements HasUlidIdInterface, TimestampableInterface
{
    use UlidIdTrait;
    use TimestampableTrait;

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
     * Structured content blocks. Empty array until the block editor lands.
     *
     * @var array<int, array<string, mixed>>
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
     * @return array<int, array<string, mixed>>
     */
    public function getContent(): array
    {
        return $this->content;
    }

    /**
     * @param array<int, array<string, mixed>> $content
     */
    public function setContent(array $content): void
    {
        $this->content = $content;
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
