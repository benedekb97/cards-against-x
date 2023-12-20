<?php

declare(strict_types=1);

namespace Unit\Checker;

use App\Checker\UserUpdateLobbyActionChecker;
use App\Checker\UserUpdateLobbyActionCheckerInterface;
use App\Entity\Game;
use App\Entity\Player;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserUpdateLobbyActionCheckerTest extends TestCase
{
    private UserUpdateLobbyActionCheckerInterface $userUpdateLobbyActionChecker;

    public function setUp(): void
    {
        parent::setUp();

        $this->userUpdateLobbyActionChecker = new UserUpdateLobbyActionChecker();
    }

    public function testGameCreatedByOtherUser(): void
    {
        $user = new User();
        $user->setPlayer($player = new Player());
        $game = new Game();

        $game->addPlayer($player);
        $game->setCreatedBy(new User());

        $this->assertFalse($this->userUpdateLobbyActionChecker->check($user, $game));
    }

    public function testAllConditionsMet(): void
    {
        $user = new User();
        $user->setPlayer($player = new Player());
        $game = new Game();

        $game->addPlayer($player);
        $game->setCreatedBy($user);

        $this->assertTrue($this->userUpdateLobbyActionChecker->check($user, $game));
    }
}