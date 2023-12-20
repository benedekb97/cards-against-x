<?php

declare(strict_types=1);

namespace App\Import\Strategy;

use App\Import\DTO\DeckImportDTO;

interface ImportStrategyInterface
{
    public const FILE_TYPE_YAML = 'yaml';
    public const FILE_TYPE_XML = 'xml';
    public const FILE_TYPE_JSON = 'json';
    public const FILE_TYPE_CSV = 'csv';

    public function supports(string $fileType): bool;

    public function import(string $filePath): DeckImportDTO;
}