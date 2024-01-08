<?php

declare(strict_types=1);

namespace Unit\Processor\Game;

use App\Entity\Enum\GameStatus;
use App\Entity\Game;
use App\Entity\Round;
use App\Processor\Game\GameStatusProcessor;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class GameStatusProcessorTest extends TestCase
{
    private GameStatusProcessor $gameStatusProcessor;

    public function setUp(): void
    {
        $this->gameStatusProcessor = new GameStatusProcessor(
            $this->createMock(EntityManagerInterface::class)
        );
    }

    public function testInProgress(): void
    {
        $game = new Game(status: GameStatus::IN_PROGRESS);

        $game->setCurrentRound($round = new Round());
        $game->addRound($round);

        $this->gameStatusProcessor->process($game);

        $this->assertEquals(GameStatus::IN_PROGRESS, $game->getStatus());
    }

    public function testLobby(): void
    {
        $game = new Game(status: GameStatus::LOBBY);

        $this->gameStatusProcessor->process($game);

        $this->assertEquals(GameStatus::LOBBY, $game->getStatus());
    }
}