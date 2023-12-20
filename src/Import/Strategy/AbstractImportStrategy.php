<?php

declare(strict_types=1);

namespace App\Import\Strategy;

use App\Import\DTO\BlackCardDTO;
use App\Import\DTO\DeckImportDTO;
use App\Import\DTO\WhiteCardDTO;

abstract class AbstractImportStrategy implements ImportStrategyInterface
{
    public function import(string $filePath): DeckImportDTO
    {
        $dto = new DeckImportDTO();

        $file = $this->parseFile($filePath);

        $this->importName($file, $dto);
        $this->importPublicity($file, $dto);
        $this->importWhiteCards($file, $dto);
        $this->importBlackCards($file, $dto);

        return $dto;
    }

    abstract protected function parseFile(string $filePath): array|\Iterator;

    protected function importName(array $file, DeckImportDTO $dto): void
    {
        $dto->setName(
            array_key_exists('name', $file) ? $file['name'] : null
        );
    }

    protected function importPublicity(array $file, DeckImportDTO $dto): void
    {
        $dto->setPublic(
            array_key_exists('public', $file) && $file['public']
        );
    }

    protected function importWhiteCards(array $file, DeckImportDTO $dto): void
    {
        if (!array_key_exists('cards', $file)) {
            return;
        }

        $cards = $file['cards'];

        if (!array_key_exists('white', $cards)) {
            return;
        }

        foreach ($cards['white'] as $whiteCard) {
            if (!is_string($whiteCard)) {
                continue;
            }

            $dto->addWhiteCard(new WhiteCardDTO($whiteCard));
        }
    }

    protected function importBlackCards(array $file, DeckImportDTO $dto): void
    {
        if (!array_key_exists('cards', $file)) {
            return;
        }

        $cards = $file['cards'];

        if (!array_key_exists('black', $cards)) {
            return;
        }

        foreach ($cards['black'] as $blackCard) {
            if (!is_array($blackCard) || !array_key_exists('parts', $blackCard)) {
                continue;
            }

            $blackCardDto = new BlackCardDTO();

            foreach ($blackCard['parts'] as $part) {
                $blackCardDto->addPart($part);
            }


            $dto->addBlackCard($blackCardDto);
        }
    }
}