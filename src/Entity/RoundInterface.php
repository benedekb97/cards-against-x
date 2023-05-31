<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Traits\DeletableInterface;
use App\Entity\Traits\HasGameInterface;
use App\Entity\Traits\ResourceInterface;
use App\Entity\Traits\TimestampableInterface;
use Doctrine\Common\Collections\Collection;

interface RoundInterface extends ResourceInterface, HasGameInterface, TimestampableInterface, DeletableInterface
{
    public function getCurrentTurn(): ?TurnInterface;

    public function setCurrentTurn(?TurnInterface $currentTurn): void;

    public function getNumber(): ?int;

    public function setNumber(?int $number): void;

    public function getTurns(): Collection;

    public function hasTurn(TurnInterface $turn): bool;

    public function addTurn(TurnInterface $turn): void;

    public function removeTurn(TurnInterface $turn): void;
}