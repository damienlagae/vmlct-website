<?php

declare(strict_types=1);

namespace App\Shared\Entity;

use Symfony\Component\Uid\Ulid;

interface HasUlidIdInterface
{
    public function getId(): ?Ulid;
}
