<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Traits\DeletableInterface;
use App\Entity\Traits\ResourceInterface;
use App\Entity\Traits\TimestampableInterface;
use Doctrine\Common\Collections\Collection;

interface TurnInterface extends ResourceInterface, DeletableInterface, TimestampableInterface
{
    public function getPlayer(): ?PlayerInterface;

    public function setPlayer(?PlayerInterface $player): void;

    public function getCard(): ?CardInterface;

    public function setCard(?CardInterface $card): void;

    public function getRound(): ?RoundInterface;

    public function setRound(?RoundInterface $round): void;

    public function getWinningPlay(): ?PlayInterface;

    public function setWinningPlay(?PlayInterface $winningPlay): void;

    public function getPlays(): Collection;

    public function hasPlay(PlayInterface $play): bool;

    public function addPlay(PlayInterface $play): void;

    public function removePlay(PlayInterface $play): void;
}