<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Traits\DeletableInterface;
use App\Entity\Traits\HasGameInterface;
use App\Entity\Traits\HasUserInterface;
use App\Entity\Traits\ResourceInterface;
use App\Entity\Traits\TimestampableInterface;
use Doctrine\Common\Collections\Collection;

interface PlayerInterface extends
    ResourceInterface,
    HasUserInterface,
    DeletableInterface,
    TimestampableInterface,
    HasGameInterface
{
    public function isReady(): bool;

    public function setReady(bool $ready): void;

    public function getVotes(): int;

    public function setVotes(int $votes): void;

    public function isVoted(): bool;

    public function setVoted(bool $voted): void;

    public function getCards(): Collection;

    public function hasCard(CardInterface $card): bool;

    public function addCard(CardInterface $card): void;

    public function removeCard(CardInterface $card): void;

    public function isHost(): bool;
}