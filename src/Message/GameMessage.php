<?php

declare(strict_types=1);

namespace App\Message;

use App\Entity\Enum\GameStatus;
use App\Entity\Enum\TurnStatus;
use Symfony\Component\Serializer\Annotation\Groups;

class GameMessage extends AbstractMessage
{
    public function __construct(
        string $url,
        #[Groups(['gameUpdate'])]
        private readonly array $players,
        #[Groups(['gameUpdate'])]
        private readonly GameStatus $gameStatus,
        #[Groups(['gameUpdate'])]
        private readonly TurnStatus $turnStatus
    ) {
        parent::__construct($url);
    }

    public function getPlayers(): array
    {
        return $this->players;
    }

    public function getGameStatus(): GameStatus
    {
        return $this->gameStatus;
    }

    public function getTurnStatus(): TurnStatus
    {
        return $this->turnStatus;
    }
}