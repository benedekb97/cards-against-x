<?php

declare(strict_types=1);

namespace App\Import\Strategy;

use App\Import\DTO\BlackCardDTO;
use App\Import\DTO\DeckImportDTO;
use App\Import\DTO\WhiteCardDTO;
use Symfony\Component\Yaml\Yaml;

class YamlImportStrategy extends AbstractImportStrategy implements ImportStrategyInterface
{
    public function supports(string $fileType): bool
    {
        return $fileType === self::FILE_TYPE_YAML;
    }

    protected function parseFile(string $filePath): array
    {
        return Yaml::parseFile($filePath);
    }
}