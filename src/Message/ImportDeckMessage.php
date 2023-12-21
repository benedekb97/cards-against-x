<?php

declare(strict_types=1);

namespace App\Message;

readonly class ImportDeckMessage
{
    public function __construct(
        private int $deckImportId
    ) {}

    public function getDeckImportId(): int
    {
        return $this->deckImportId;
    }
}