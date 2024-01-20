<?php

declare(strict_types=1);

namespace App\Tests\Unit\Checker;

use App\Checker\UserHostEligibilityChecker;
use App\Checker\UserHostEligibilityCheckerInterface;
use App\Entity\Enum\GameStatus;
use App\Entity\Game;
use App\Entity\Player;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserHostEligibilityCheckerTest extends TestCase
{
    private UserHostEligibilityCheckerInterface $userHostEligibilityChecker;

    public function setUp(): void
    {
        parent::setUp();

        $this->userHostEligibilityChecker = new UserHostEligibilityChecker();
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
        $game->addPlayer($player);

        $this->assertFalse($this->userHostEligibilityChecker->check($user));
    }

    public function testUserHasNoPlayer(): void
    {
        $user = new User();

        $this->assertTrue($this->userHostEligibilityChecker->check($user));
    }

    public function testGameFinished(): void
    {
        $user = new User();
        $player = new Player();
        $game = new Game(status: GameStatus::FINISHED);

        $game->addPlayer($player);
        $player->setUser($user);

        $this->assertTrue($this->userHostEligibilityChecker->check($user));
        $this->assertNull($user->getPlayer());
    }

    private function gameStatusDataProvider(): array
    {
        return [
            GameStatus::IN_PROGRESS->name => [GameStatus::IN_PROGRESS],
            GameStatus::CANCELED->name => [GameStatus::CANCELED],
            GameStatus::LOBBY->name => [GameStatus::LOBBY],
        ];
    }
}