<?php

declare(strict_types=1);

namespace App\Tests\Unit\Processor\Game;

use App\Entity\Enum\GameStatus;
use App\Entity\Enum\TurnStatus;
use App\Entity\Game;
use App\Entity\Player;
use App\Entity\PlayerInterface;
use App\Entity\Round;
use App\Entity\Turn;
use App\Entity\User;
use App\Processor\Game\PlayerReadyStateProcessor;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class PlayerReadyStateProcessorTest extends TestCase
{
    private PlayerReadyStateProcessor $playerReadyStateProcessor;

    public function setUp(): void
    {
        $this->playerReadyStateProcessor = new PlayerReadyStateProcessor(
            $this->createMock(EntityManagerInterface::class)
        );
    }

    /**
     * @dataProvider gameStatusDataProvider
     * @param GameStatus $status
     * @param array $players
     * @return void
     */
    public function testInvalidGameStatuses(GameStatus $status, array $players): void
    {
        $game = new Game(status: $status);

        /** @var PlayerInterface $player */
        foreach ($players as $player) {
            $game->addPlayer(clone $player);
        }

        $this->playerReadyStateProcessor->process($game);

        /** @var PlayerInterface $player */
        foreach ($players as $player) {
            /** @var PlayerInterface $gamePlayer */
            foreach ($game->getPlayers() as $gamePlayer) {
                if ($gamePlayer->getUser() !== $player->getUser()) {
                    continue;
                }

                $this->assertEquals($player->isReady(), $gamePlayer->isReady());
            }
        }
    }

    /**
     * @dataProvider turnStatusDataProvider
     * @param TurnStatus $turnStatus
     * @param array $players
     * @return void
     */
    public function testInvalidTurnStatuses(TurnStatus $turnStatus, array $players): void
    {
        $game = new Game(status: GameStatus::IN_PROGRESS);
        $round = new Round();
        $turn = new Turn(status: $turnStatus);

        $game->addRound($round);
        $game->setCurrentRound($round);

        $round->addTurn($turn);
        $round->setCurrentTurn($turn);

        /** @var PlayerInterface $player */
        foreach ($players as $player) {
            $game->addPlayer(clone $player);
        }

        $this->playerReadyStateProcessor->process($game);

        /** @var PlayerInterface $gamePlayer */
        foreach ($game->getPlayers() as $gamePlayer) {
            /** @var PlayerInterface $player */
            foreach ($players as $player) {
                if ($player->getUser() !== $gamePlayer->getUser()) {
                    continue;
                }

                $this->assertEquals($player->isReady(), $gamePlayer->isReady());
            }
        }
    }

    public function testAllConditionsMet(): void
    {
        $players = $this->createPlayers();

        $game = new Game(status: GameStatus::IN_PROGRESS);
        $round = new Round();
        $turn = new Turn(status: TurnStatus::CHOOSING);

        $game->addRound($round);
        $game->setCurrentRound($round);

        $round->addTurn($turn);
        $round->setCurrentTurn($turn);

        foreach ($players as $player) {
            $game->addPlayer($player);
        }

        $this->playerReadyStateProcessor->process($game);

        foreach ($game->getPlayers() as $player) {
            $this->assertFalse($player->isReady());
        }
    }

    private function gameStatusDataProvider(): array
    {
        return [
            GameStatus::FINISHED->name => [GameStatus::FINISHED, $this->createPlayers()],
            GameStatus::LOBBY->name => [GameStatus::LOBBY, $this->createPlayers()],
            GameStatus::CANCELED->name => [GameStatus::CANCELED, $this->createPlayers()],
        ];
    }

    private function turnStatusDataProvider(): array
    {
        return [
            TurnStatus::IN_PROGRESS->name => [TurnStatus::IN_PROGRESS, $this->createPlayers()],
            TurnStatus::FINISHED->name => [TurnStatus::FINISHED, $this->createPlayers()],
            TurnStatus::RECAP->name => [TurnStatus::RECAP, $this->createPlayers()],
            TurnStatus::DRAFT->name => [TurnStatus::DRAFT, $this->createPlayers()],
        ];
    }

    private function createPlayers(): array
    {
        $players = [];

        $playerCount = random_int(3, 7);

        for ($i = 0; $i < $playerCount; $i++) {
            $player = new Player(ready: (bool)random_int(0, 1));

            $user = new User();

            $user->setInternalId('user_' . random_int(10,99));

            $user->setPlayer($player);

            $player->setUser($user);

            $players[] = $player;
        }

        return $players;
    }
}