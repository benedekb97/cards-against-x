<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Enum\CardType;
use App\Entity\Traits\CreatedByUserInterface;
use App\Entity\Traits\DeletableInterface;
use App\Entity\Traits\ResourceInterface;
use App\Entity\Traits\TimestampableInterface;
use Doctrine\Common\Collections\Collection;

interface CardInterface extends ResourceInterface, TimestampableInterface, DeletableInterface, CreatedByUserInterface
{
    public function getType(): ?CardType;

    public function setType(?CardType $type): void;

    public function getText(): ?array;

    public function getFormattedText(): string;

    public function setText(?array $text): void;

    public function getDecks(): Collection;

    public function hasDeck(DeckInterface $deck): bool;

    public function addDeck(DeckInterface $deck): void;

    public function removeDeck(DeckInterface $deck): void;

    public function getBlankCount(): int;
}