<?php

declare(strict_types=1);

namespace App\Entity\Traits;

interface DeletableInterface
{
    public function getDeletedAt(): ?\DateTimeInterface;

    public function delete(): void;

    public function restore(): void;
}