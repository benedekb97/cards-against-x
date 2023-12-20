<?php

declare(strict_types=1);

namespace App\Import\DTO;

class DeckImportDTO
{
    private ?string $name = null;

    private bool $public = false;

    private array $whiteCards = [];

    private array $blackCards = [];

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): DeckImportDTO
    {
        $this->name = $name;
        return $this;
    }

    public function isPublic(): bool
    {
        return $this->public;
    }

    public function setPublic(bool $public): self
    {
        $this->public = $public;

        return $this;
    }

    public function getWhiteCards(): array
    {
        return $this->whiteCards;
    }

    public function setWhiteCards(array $whiteCards): DeckImportDTO
    {
        $this->whiteCards = $whiteCards;
        return $this;
    }

    public function addWhiteCard(WhiteCardDTO $whiteCardDTO): self
    {
        $this->whiteCards[] = $whiteCardDTO;

        return $this;
    }

    public function getBlackCards(): array
    {
        return $this->blackCards;
    }

    public function setBlackCards(array $blackCards): DeckImportDTO
    {
        $this->blackCards = $blackCards;
        return $this;
    }

    public function addBlackCard(BlackCardDTO $blackCard): self
    {
        $this->blackCards[] = $blackCard;

        return $this;
    }
}