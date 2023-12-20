<?php

declare(strict_types=1);

namespace Unit\Checker;

use App\Checker\UserWinnerSubmitActionChecker;
use App\Checker\UserWinnerSubmitActionCheckerInterface;
use App\Entity\Enum\TurnStatus;
use App\Entity\Game;
use App\Entity\Play;
use App\Entity\Player;
use App\Entity\Round;
use App\Entity\Turn;
use App\Entity\User;
use App\Entity\UserInterface;
use PHPUnit\Framework\TestCase;

class UserWinnerSubmitActionCheckerTest extends TestCase
{
    private UserWinnerSubmitActionCheckerInterface $userWinnerSubmitActionChecker;

    public function setUp(): void
    {
        parent::setUp();

        $this->userWinnerSubmitActionChecker = new UserWinnerSubmitActionChecker();
    }

    public function testInvalidUser(): void
    {
        $user = new User();

        $this->assertFalse($this->userWinnerSubmitActionChecker->check($user));

        $player = new Player();

        $user->setPlayer($player);
        $player->setUser($user);

        $this->assertFalse($this->userWinnerSubmitActionChecker->check($user));

        $game = new Game();

        $game->addPlayer($player);

        $this->assertFalse($this->userWinnerSubmitActionChecker->check($user));

        $round = new Round();

        $game->addRound($round);
        $game->setCurrentRound($round);

        $this->assertFalse($this->userWinnerSubmitActionChecker->check($user));
    }

    public function testInvalidPlayer(): void
    {
        $user = $this->createUser();

        $user->getPlayer()->getGame()->getCurrentRound()->getCurrentTurn()->setPlayer(new Player());

        $this->assertFalse($this->userWinnerSubmitActionChecker->check($user));
    }

    /**
     * @dataProvider turnStatusDataProvider
     * @param TurnStatus $status
     * @return void
     */
    public function testInvalidTurnStatuses(TurnStatus $status): void
    {
        $user = $this->createUser();

        $turn = $user
            ->getPlayer()
            ->getGame()
            ->getCurrentRound()
            ->getCurrentTurn();

        $turn->setStatus($status);
        $turn->setPlayer($user->getPlayer());

        $this->assertFalse($this->userWinnerSubmitActionChecker->check($user));
    }

    public function testAllConditionsMet(): void
    {
        $user = $this->createUser();

        $turn = $user
            ->getPlayer()
            ->getGame()
            ->getCurrentRound()
            ->getCurrentTurn();

        $turn->setPlayer($user->getPlayer());
        $turn->setStatus(TurnStatus::CHOOSING);

        $this->assertTrue($this->userWinnerSubmitActionChecker->check($user));
    }

    private function turnStatusDataProvider(): array
    {
        return [
            TurnStatus::IN_PROGRESS->name => [TurnStatus::IN_PROGRESS],
            TurnStatus::RECAP->name => [TurnStatus::RECAP],
            TurnStatus::DRAFT->name => [TurnStatus::DRAFT],
            TurnStatus::FINISHED->name => [TurnStatus::FINISHED],
        ];
    }

    private function createUser(): UserInterface
    {
        $user = new User();
        $player = new Player();
        $game = new Game();
        $round = new Round();
        $turn = new Turn();

        $user->setPlayer($player);
        $player->setUser($user);
        $game->addPlayer($player);
        $game->addRound($round);
        $game->setCurrentRound($round);
        $round->addTurn($turn);
        $round->setCurrentTurn($turn);

        return $user;
    }
}