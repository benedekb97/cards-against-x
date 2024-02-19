<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Enum\ImportStatus;
use App\Entity\Traits\CreatedByUserInterface;
use App\Entity\Traits\ResourceInterface;
use App\Entity\Traits\TimestampableInterface;

interface DeckImportInterface extends ResourceInterface, CreatedByUserInterface, TimestampableInterface
{
    public function getFilePath(): ?string;

    public function setFilePath(?string $filePath): void;

    public function getErrorString(): ?string;

    public function setErrorString(?string $errorString): void;

    public function getStatus(): ImportStatus;

    public function setStatus(ImportStatus $status): void;

    public function getDeck(): ?DeckInterface;

    public function setDeck(?DeckInterface $deck): void;
}