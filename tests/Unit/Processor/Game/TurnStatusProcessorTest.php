<?php

declare(strict_types=1);

namespace App\Tests\Unit\Processor\Game;

use App\Entity\Enum\TurnStatus;
use App\Entity\Game;
use App\Entity\GameInterface;
use App\Entity\Play;
use App\Entity\Player;
use App\Entity\PlayerInterface;
use App\Entity\Round;
use App\Entity\Turn;
use App\Processor\Game\TurnStatusProcessor;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class TurnStatusProcessorTest extends TestCase
{
    private TurnStatusProcessor $turnStatusProcessor;

    public function setUp(): void
    {
        $this->turnStatusProcessor = new TurnStatusProcessor(
            $this->createMock(EntityManagerInterface::class)
        );
    }

    public function testNoPlayersPlayed(): void
    {
        $game = $this->setUpGame();

        $this->turnStatusProcessor->process($game);

        $this->assertEquals(TurnStatus::IN_PROGRESS, $game->getCurrentRound()->getCurrentTurn()->getStatus());
    }

    public function testSomePlayersPlayed(): void
    {
        $game = $this->setUpGame();

        $count = 0;

        /** @var PlayerInterface $player */
        foreach ($game->getPlayers() as $player) {
            if ((bool) random_int(0, 1) && $count !== 0) {
                $play = new Play();

                $player->addPlay($play);
                $game->getCurrentRound()->getCurrentTurn()->addPlay($play);
            } else {
                $count++;
            }
        }

        $this->turnStatusProcessor->process($game);

        $this->assertEquals(TurnStatus::IN_PROGRESS, $game->getCurrentRound()->getCurrentTurn()->getStatus());
    }

    public function testAllPlayersPlayed(): void
    {
        $game = $this->setUpGameWithAllPlayersPlayed();

        $this->turnStatusProcessor->process($game);

        $this->assertEquals(TurnStatus::CHOOSING, $game->getCurrentRound()->getCurrentTurn()->getStatus());
    }

    public function testWinnerChosen(): void
    {
        $game = $this->setUpGameWithAllPlayersPlayedAndWinnerChosen();

        $this->turnStatusProcessor->process($game);

        $this->assertEquals(TurnStatus::RECAP, $game->getCurrentRound()->getCurrentTurn()->getStatus());
    }

    public function testSomePlayersReady(): void
    {
        $game = $this->setUpGameWithAllPlayersPlayedAndWinnerChosen();

        $count = 0;

        /** @var PlayerInterface $player */
        foreach ($game->getPlayers() as $player) {
            if (random_int(0, 1) && $count !== 0) {
                $player->setReady(true);
            } else {
                $count++;
            }
        }

        $this->turnStatusProcessor->process($game);

        $this->assertEquals(TurnStatus::RECAP, $game->getCurrentRound()->getCurrentTurn()->getStatus());
    }

    public function testAllPlayersReady(): void
    {
        $game = $this->setUpGameWithAllPlayersPlayedAndWinnerChosen();

        /** @var PlayerInterface $player */
        foreach ($game->getPlayers() as $player) {
            $player->setReady(true);
        }

        $this->turnStatusProcessor->process($game);

        $this->assertEquals(TurnStatus::FINISHED, $game->getCurrentRound()->getCurrentTurn()->getStatus());
    }

    private function setUpGameWithAllPlayersPlayedAndWinnerChosen(): GameInterface
    {
        $game = $this->setUpGameWithAllPlayersPlayed();

        $plays = $game->getCurrentRound()->getCurrentTurn()->getPlays();

        $play = $plays->get(array_rand($plays->toArray()));

        $game->getCurrentRound()->getCurrentTurn()->setWinningPlay($play);

        return $game;
    }

    private function setUpGameWithAllPlayersPlayed(): GameInterface
    {
        $game = $this->setUpGame();

        /** @var PlayerInterface $player */
        foreach ($game->getPlayers() as $player) {
            if ($game->getCurrentRound()->getCurrentTurn()->getPlayer() === $player) {
                continue;
            }

            $play = new Play();

            $player->addPlay($play);
            $game->getCurrentRound()->getCurrentTurn()->addPlay($play);
        }

        return $game;
    }

    private function setUpGame(): GameInterface
    {
        $game = new Game();
        $round = new Round();
        $turn = new Turn();

        $players = $this->getPlayers();

        foreach ($players as $player) {
            $game->addPlayer($player);
        }

        $player = $players[array_rand($players)];

        $turn->setPlayer($player);

        $game->addRound($round);
        $game->setCurrentRound($round);

        $round->addTurn($turn);
        $round->setCurrentTurn($turn);

        return $game;
    }

    private function getPlayers(): array
    {
        $playerCount = random_int(3, 7);

        $players = [];

        for ($i = 0; $i < $playerCount; $i++) {
            $player = new Player();

            $players[] = $player;
        }

        return $players;
    }
}