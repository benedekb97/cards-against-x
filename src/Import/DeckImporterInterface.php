<?php

declare(strict_types=1);

namespace App\Import;

use App\Entity\DeckImportInterface;
use App\Import\Strategy\ImportStrategyInterface;

interface DeckImporterInterface
{
    public function import(DeckImportInterface $deckImport): void;

    public function setImportStrategy(ImportStrategyInterface $importStrategy): void;
}