<?php

declare(strict_types=1);

namespace App\Import\DTO;

readonly class WhiteCardDTO
{
    public function __construct(
        private string $text,
    ) {}

    public function getText(): string
    {
        return $this->text;
    }
}