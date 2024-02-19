<?php

declare(strict_types=1);

namespace App\Import\Strategy;

use Symfony\Component\Serializer\Encoder\DecoderInterface;

class XmlImportStrategy extends AbstractImportStrategy implements ImportStrategyInterface
{
    public function __construct(
        private readonly DecoderInterface $decoder
    ) {}

    protected function parseFile(string $filePath): array
    {
        $fileContents = file_get_contents($filePath);

        return $this->decoder->decode($fileContents, 'xml');
    }

    public function supports(string $fileType): bool
    {
        return self::FILE_TYPE_XML === $fileType;
    }
}