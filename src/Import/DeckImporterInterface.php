<?php

declare(strict_types=1);

namespace App\Import;

use App\Entity\UserInterface;
use App\Import\Strategy\ImportStrategyInterface;

interface DeckImporterInterface
{
    public function import(string $filePath, int $userId): void;

    public function setImportStrategy(ImportStrategyInterface $importStrategy): void;
}