<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Traits\CreatedByUserInterface;
use App\Entity\Traits\DeletableInterface;
use App\Entity\Traits\ResourceInterface;
use App\Entity\Traits\TimestampableInterface;
use Doctrine\Common\Collections\Collection;

interface DeckInterface extends ResourceInterface, CreatedByUserInterface, TimestampableInterface, DeletableInterface
{
    public function getName(): ?string;

    public function setName(?string $name): void;

    public function isPublic(): bool;

    public function setPublic(bool $public): void;

    public function getCards(): Collection;

    public function hasCard(CardInterface $card): bool;

    public function addCard(CardInterface $card): void;

    public function removeCard(CardInterface $card): void;
}