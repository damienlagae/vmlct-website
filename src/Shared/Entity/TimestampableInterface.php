<?php

declare(strict_types=1);

namespace App\Shared\Entity;

interface TimestampableInterface
{
    public function getCreatedAt(): ?\DateTimeImmutable;

    public function getUpdatedAt(): ?\DateTimeImmutable;
}
