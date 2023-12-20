<?php

declare(strict_types=1);

namespace Unit\Checker;

use App\Checker\UserJoinActionChecker;
use App\Checker\UserJoinActionCheckerInterface;
use App\Entity\Enum\GameStatus;
use App\Entity\Game;
use App\Entity\Player;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserJoinActionCheckerTest extends TestCase
{
    private UserJoinActionCheckerInterface $userJoinActionChecker;

    public function setUp(): void
    {
        parent::setUp();

        $this->userJoinActionChecker = new UserJoinActionChecker();
    }

    public function testUserInGame(): void
    {
        $user = new User();
        $player = new Player();
        $game = new Game();

        $game->addPlayer($player);
        $user->setPlayer($player);
        $player->setUser($user);

        $this->assertFalse($this->userJoinActionChecker->check($user, $game));
    }

    public function testUserInOtherGame(): void
    {
        $user = new User();
        $player = new Player();
        $game = new Game();

        $game->addPlayer($player);
        $user->setPlayer($player);
        $player->setUser($user);

        $this->assertFalse($this->userJoinActionChecker->check($user, new Game()));
    }

    /**
     * @dataProvider gameStatusDataProvider
     * @param GameStatus $status
     * @return void
     */
    public function testInvalidGameStatuses(GameStatus $status): void
    {
        $this->assertFalse(
            $this->userJoinActionChecker->check(new User(), new Game(status: $status))
        );
    }

    public function testAllRequirementsMet(): void
    {
        $user = new User();
        $game = new Game(status: GameStatus::LOBBY);

        $this->assertTrue(
            $this->userJoinActionChecker->check($user, $game)
        );
    }

    private function gameStatusDataProvider(): array
    {
        return [
            GameStatus::IN_PROGRESS->name => [GameStatus::IN_PROGRESS],
            GameStatus::CANCELED->name => [GameStatus::CANCELED],
            GameStatus::FINISHED->name => [GameStatus::FINISHED],
        ];
    }
}