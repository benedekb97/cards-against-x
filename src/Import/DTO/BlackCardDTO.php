<?php

declare(strict_types=1);

namespace App\Import\DTO;

class BlackCardDTO
{
    private array $parts = [];

    public function getParts(): array
    {
        return $this->parts;
    }

    public function addPart(string $part): self
    {
        $this->parts[] = $part;

        return $this;
    }
}