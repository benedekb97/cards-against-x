<?php

declare(strict_types=1);

namespace App\Tests\Unit\Checker;

use App\Checker\UserCardSubmitActionChecker;
use App\Checker\UserCardSubmitActionCheckerInterface;
use App\Entity\Enum\GameStatus;
use App\Entity\Enum\TurnStatus;
use App\Entity\Game;
use App\Entity\Play;
use App\Entity\Player;
use App\Entity\Round;
use App\Entity\Turn;
use App\Entity\User;
use PHPUnit\Framework\MockObject\MockMethodSet;
use PHPUnit\Framework\TestCase;

class UserCardSubmitActionCheckerTest extends TestCase
{
    private UserCardSubmitActionCheckerInterface $cardSubmitActionChecker;

    public function setUp(): void
    {
        parent::setUp();

        $this->cardSubmitActionChecker = new UserCardSubmitActionChecker();
    }

    public function testUserNotInGame(): void
    {
        $user = new User();

        $this->assertFalse(
            $this->cardSubmitActionChecker->check($user)
        );
    }

    public function testPlayerHasNoGame(): void
    {
        $user = new User();
        $player = new Player();

        $user->setPlayer($player);

        $this->assertFalse(
            $this->cardSubmitActionChecker->check($user)
        );
    }

    /**
     * @dataProvider gameStatusDataProvider
     * @param GameStatus $status
     * @return void
     */
    public function testInvalidGameStatuses(GameStatus $status): void
    {
        $user = new User();
        $player = new Player();
        $game = new Game(status: $status);

        $user->setPlayer($player);
        $player->setGame($game);

        $this->assertFalse(
            $this->cardSubmitActionChecker->check($user)
        );
    }

    public function testGameHasNoRound(): void
    {
        $user = new User();
        $player = new Player();
        $game = new Game(status: GameStatus::IN_PROGRESS);

        $user->setPlayer($player);
        $game->addPlayer($player);

        $this->assertFalse($this->cardSubmitActionChecker->check($user));
    }

    public function testRoundHasNoTurn(): void
    {
        $user = new User();
        $player = new Player();
        $game = new Game(status: GameStatus::IN_PROGRESS);
        $round = new Round();

        $user->setPlayer($player);
        $game->addPlayer($player);
        $game->addRound($round);

        $this->assertFalse($this->cardSubmitActionChecker->check($user));
    }

    public function testTurnHostIsPlayer(): void
    {
        $user = new User();
        $player = new Player();
        $game = new Game(status: GameStatus::IN_PROGRESS);
        $round = new Round();
        $turn = new Turn();

        $user->setPlayer($player);
        $game->addPlayer($player);
        $round->addTurn($turn);
        $game->addRound($round);
        
        $this->assertFalse($this->cardSubmitActionChecker->check($user));
        
        $turn->setPlayer($player);
        
        $this->assertFalse($this->cardSubmitActionChecker->check($user));
    }

    /**
     * @dataProvider turnStatusDataProvider
     * @param TurnStatus $status
     * @return void
     */
    public function testInvalidTurnStatuses(TurnStatus $status): void
    {
        $user = new User();
        $player = new Player();
        $game = new Game(status: GameStatus::IN_PROGRESS);
        $round = new Round();
        $turn = new Turn(status: $status);

        $player->setUser($user);
        $game->addPlayer($player);
        $game->addRound($round);
        $round->addTurn($turn);

        $this->assertFalse($this->cardSubmitActionChecker->check($user));
    }

    public function testPlayerAlreadyPlayed(): void
    {
        $user = new User();
        $player = new Player();
        $game = new Game(status: GameStatus::IN_PROGRESS);
        $round = new Round();
        $turn = new Turn(status: TurnStatus::IN_PROGRESS);
        $play = new Play();

        $player->addPlay($play);
        $player->setUser($user);
        $game->addPlayer($player);
        $game->addRound($round);
        $round->addTurn($turn);

        $this->assertFalse($this->cardSubmitActionChecker->check($user));
    }

    private function gameStatusDataProvider(): array
    {
        return [
            GameStatus::FINISHED->name => [GameStatus::FINISHED],
            GameStatus::CANCELED->name => [GameStatus::CANCELED],
            GameStatus::LOBBY->name => [GameStatus::LOBBY],
        ];
    }

    private function turnStatusDataProvider(): array
    {
        return [
            TurnStatus::CHOOSING->name => [TurnStatus::CHOOSING],
            TurnStatus::RECAP->name => [TurnStatus::RECAP],
            TurnStatus::DRAFT->name => [TurnStatus::DRAFT],
            TurnStatus::FINISHED->name => [TurnStatus::FINISHED],
        ];
    }
}