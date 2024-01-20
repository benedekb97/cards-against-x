<?php

declare(strict_types=1);

namespace App\Tests\Unit\Checker;

use App\Checker\UserLeaveActionChecker;
use App\Checker\UserLeaveActionCheckerInterface;
use App\Entity\Enum\GameStatus;
use App\Entity\Game;
use App\Entity\Player;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserLeaveActionCheckerTest extends TestCase
{
    private UserLeaveActionCheckerInterface $userLeaveActionChecker;

    public function setUp(): void
    {
        parent::setUp();

        $this->userLeaveActionChecker = new UserLeaveActionChecker();
    }

    public function testGameInProgress(): void
    {
        $user = new User();
        $player = new Player();
        $game = new Game(status: GameStatus::IN_PROGRESS);

        $player->setUser($user);
        $user->setPlayer($player);

        $game->addPlayer($player);

        $this->assertFalse($this->userLeaveActionChecker->check($user));
    }

    /**
     * @dataProvider gameStatusDataProvider
     * @param GameStatus $status
     * @return void
     */
    public function testValidGameStatuses(GameStatus $status): void
    {
        $user = new User();
        $player = new Player();
        $game = new Game(status: $status);

        $player->setUser($user);
        $user->setPlayer($player);

        $game->addPlayer($player);

        $this->assertTrue($this->userLeaveActionChecker->check($user));
    }

    private function gameStatusDataProvider(): array
    {
        return [
            GameStatus::FINISHED->name => [GameStatus::FINISHED],
            GameStatus::CANCELED->name => [GameStatus::CANCELED],
            GameStatus::LOBBY->name => [GameStatus::LOBBY],
        ];
    }
}