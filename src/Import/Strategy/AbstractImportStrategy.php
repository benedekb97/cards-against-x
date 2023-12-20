<?php

declare(strict_types=1);

namespace App\Import\Strategy;

use App\Import\DTO\DeckImportDTO;
use Symfony\Component\Yaml\Yaml;

abstract class AbstractImportStrategy implements ImportStrategyInterface
{
    abstract public function import(string $filePath): DeckImportDTO;
}