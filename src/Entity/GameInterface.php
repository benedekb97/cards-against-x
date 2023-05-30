<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Enum\GameStatus;
use App\Entity\Traits\CreatedByUserInterface;
use App\Entity\Traits\DeletableInterface;
use App\Entity\Traits\ResourceInterface;
use App\Entity\Traits\TimestampableInterface;
use Doctrine\Common\Collections\Collection;

interface GameInterface extends ResourceInterface, TimestampableInterface, DeletableInterface, CreatedByUserInterface
{
    public const DEFAULT_ROUND_COUNT = 3;

    public function getSlug(): ?string;

    public function setSlug(?string $slug): void;

    public function getNumberOfRounds(): ?int;

    public function setNumberOfRounds(?int $numberOfRounds): void;

    public function getStatus(): GameStatus;

    public function setStatus(GameStatus $status): void;

    public function getCurrentRound(): ?RoundInterface;

    public function setCurrentRound(?RoundInterface $currentRound): void;

    public function getDeck(): ?DeckInterface;

    public function setDeck(?DeckInterface $deck): void;

    public function isSpectatable(): bool;

    public function setSpectatable(bool $spectatable): void;

    public function getMessages(): Collection;

    public function hasMessage(MessageInterface $message): bool;

    public function addMessage(MessageInterface $message): void;

    public function removeMessage(MessageInterface $message): void;

    public function getPlayers(): Collection;

    public function hasPlayer(PlayerInterface $player): bool;

    public function addPlayer(PlayerInterface $player): void;

    public function removePlayer(PlayerInterface $player): void;

    public function getRounds(): Collection;

    public function hasRound(RoundInterface $round): bool;

    public function addRound(RoundInterface $round): void;

    public function removeRound(RoundInterface $round): void;
}