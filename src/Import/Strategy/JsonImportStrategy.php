<?php

declare(strict_types=1);

namespace App\Import\Strategy;

use App\Import\DTO\DeckImportDTO;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Mime\Part\File;
use Symfony\Component\Serializer\Encoder\DecoderInterface;

class JsonImportStrategy extends AbstractImportStrategy implements ImportStrategyInterface
{
    public function __construct(
        private readonly DecoderInterface $decoder,
    ) {}

    public function supports(string $fileType): bool
    {
        return self::FILE_TYPE_JSON === $fileType;
    }

    protected function parseFile(string $filePath): array
    {
        $fileContents = file_get_contents($filePath);

        return $this->decoder->decode($fileContents, 'json');
    }
}