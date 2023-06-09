<?php

declare(strict_types=1);

namespace App\Message;

use App\Entity\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;

class LobbyMessage extends AbstractMessage
{
    public function __construct(
        string $url,
        #[Groups(['lobbyUpdate'])]
        private readonly int $numberOfRounds,
        #[Groups(['lobbyUpdate'])]
        private readonly ?int $deckId,
        /** @var array|UserInterface[] $users */
        #[Groups(['lobbyUpdate'])]
        private readonly array $users,
        #[Groups(['lobbyUpdate'])]
        private readonly bool $readyToStart,
        #[Groups(['lobbyUpdate'])]
        private readonly string $status
    )
    {
        parent::__construct($url);
    }

    public function getNumberOfRounds(): int
    {
        return $this->numberOfRounds;
    }

    public function getDeckId(): ?int
    {
        return $this->deckId;
    }

    public function getUsers(): array
    {
        return $this->users;
    }

    public function isReadyToStart(): bool
    {
        return $this->readyToStart;
    }

    public function getStatus(): string
    {
        return $this->status;
    }
}