<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Traits\DeletableInterface;
use App\Entity\Traits\ResourceInterface;
use App\Entity\Traits\TimestampableInterface;
use Doctrine\Common\Collections\Collection;

interface PlayInterface extends ResourceInterface, DeletableInterface, TimestampableInterface
{
    public function getPlayer(): ?PlayerInterface;

    public function setPlayer(?PlayerInterface $player): void;

    public function getTurn(): ?TurnInterface;

    public function setTurn(?TurnInterface $turn): void;

    public function getPoints(): ?int;

    public function setPoints(?int $points): void;

    public function getLikes(): int;

    public function setLikes(int $likes): void;

    public function isFeatured(): bool;

    public function setFeatured(bool $featured): void;

    public function getCards(): Collection;

    public function hasCard(CardInterface $card): bool;

    public function addCard(CardInterface $card): void;

    public function removeCard(CardInterface $card): void;

    public function getPlayHTML(): string;
}