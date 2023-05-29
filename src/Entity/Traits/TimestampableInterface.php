<?php

declare(strict_types=1);

namespace App\Entity\Traits;

interface TimestampableInterface
{
    public function getCreatedAt(): ?\DateTimeImmutable;

    public function setCreatedAtNow(): void;

    public function getUpdatedAt(): ?\DateTimeInterface;

    public function setUpdatedAtNow(): void;
}