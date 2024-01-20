<?php

namespace App\Tests\Unit\Checker;

use App\Checker\UserStartActionChecker;
use App\Checker\UserStartActionCheckerInterface;
use App\Entity\Deck;
use App\Entity\Enum\GameStatus;
use App\Entity\Game;
use App\Entity\Play;
use App\Entity\Player;
use App\Entity\User;
use App\Entity\UserInterface;
use PHPUnit\Framework\TestCase;

class UserStartActionCheckerTest extends TestCase
{
    private UserStartActionCheckerInterface $userStartActionChecker;

    public function setUp(): void
    {
        parent::setUp();

        $this->userStartActionChecker = new UserStartActionChecker();
    }

    public function testUserNotInGame(): void
    {
        $user = new User();

        $this->assertFalse($this->userStartActionChecker->check($user));

        $player = new Player();

        $user->setPlayer($player);
        $player->setUser($user);

        $this->assertFalse($this->userStartActionChecker->check($user));
    }

    public function testGameCreatedByOtherUser(): void
    {
        $user = new User();
        $player = new Player();
        $game = new Game();

        $user->setPlayer($player);
        $player->setUser($user);
        $game->addPlayer($player);
        $game->setCreatedBy(new User());

        $this->assertFalse($this->userStartActionChecker->check($user));
    }

    /**
     * @dataProvider gameStatusDataProvider
     * @param GameStatus $status
     * @return void
     */
    public function testInvalidGameStatus(GameStatus $status): void
    {
        $user = $this->createValidUser();

        $user->getPlayer()->getGame()->setStatus($status);

        $this->assertFalse($this->userStartActionChecker->check($user));
    }

    public function testNotAllPlayersReady(): void
    {
        $user = $this->createValidUser();
        $player = new Player(ready: false);

        $game = $user->getPlayer()->getGame();
        $game->addPlayer($player);

        $this->assertFalse($this->userStartActionChecker->check($user));
    }

    public function testOnlyOnePlayer(): void
    {
        $user = $this->createValidUser();

        $user->getPlayer()->setReady(true);

        $this->assertFalse($this->userStartActionChecker->check($user));
    }

    public function testAllConditionsMet(): void
    {
        $user = $this->createValidUser();

        $game = $user->getPlayer()->getGame();

        $game->addPlayer(new Player(ready: true));
        $user->getPlayer()->setReady(true);

        $game->setDeck(new Deck());

        $this->assertTrue($this->userStartActionChecker->check($user));
    }

    private function gameStatusDataProvider(): array
    {
        return [
            GameStatus::IN_PROGRESS->name => [GameStatus::IN_PROGRESS],
            GameStatus::CANCELED->name => [GameStatus::CANCELED],
            GameStatus::FINISHED->name => [GameStatus::FINISHED],
        ];
    }

    private function createValidUser(): UserInterface
    {
        $user = new User();
        $player = new Player();
        $game = new Game(status: GameStatus::LOBBY);

        $user->setPlayer($player);
        $player->setUser($user);
        $game->addPlayer($player);
        $game->setCreatedBy($user);

        return $user;
    }
}
