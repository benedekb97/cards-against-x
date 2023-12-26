<?php

declare(strict_types=1);

namespace App\Import\Strategy;

use App\Entity\Enum\CardType;
use App\Import\DTO\BlackCardDTO;
use App\Import\DTO\DeckImportDTO;
use App\Import\DTO\WhiteCardDTO;
use League\Csv\Reader;

class CsvImportStrategy extends AbstractImportStrategy implements ImportStrategyInterface
{
    public const HEADER_NAME = 'Name';
    public const HEADER_TYPE = 'Type';
    public const HEADER_PARTS = 'Parts';
    public const HEADER_TEXT = 'Text';

    public function supports(string $fileType): bool
    {
        return self::FILE_TYPE_CSV === $fileType;
    }

    protected function parseFile(string $filePath): array|\Iterator
    {
        throw new \Exception('parseFile method should not be called on CsvImportStrategy!');
    }

    public function import(string $filePath): DeckImportDTO
    {
        $dto = new DeckImportDTO();

        $reader = Reader::createFromPath($filePath);

        $reader->setHeaderOffset(0);

        $reader->each(
            function ($row) use ($dto) {
                $this->parseRow($row, $dto);
            }
        );

        return $dto;
    }

    private function parseRow(array $row, DeckImportDTO $dto): void
    {
        foreach ($row as $headerValue => $value) {
            if (null === $dto->getName() && self::HEADER_NAME === $headerValue) {
                $dto->setName($value);
            }

            if (self::HEADER_TYPE === $headerValue && $value === CardType::WHITE->value) {
                $dto->addWhiteCard(
                    new WhiteCardDTO($row[$this->getWhiteTextIndex($row)])
                );

                return;
            }

            if (self::HEADER_TYPE === $headerValue && $value === CardType::BLACK->value) {
                $blackCard = new BlackCardDTO();

                $parts = explode(',', $row[$this->getBlackTextIndex($row)]);

                foreach ($parts as $part) {
                    $blackCard->addPart($part);
                }

                $dto->addBlackCard($blackCard);

                return;
            }
        }
    }

    private function getWhiteTextIndex(array $row): string
    {
        return !empty($row[self::HEADER_TEXT]) ? self::HEADER_TEXT : self::HEADER_PARTS;
    }

    private function getBlackTextIndex(array $row): string
    {
        return !empty($row[self::HEADER_PARTS]) ? self::HEADER_PARTS : self::HEADER_TEXT;
    }
}