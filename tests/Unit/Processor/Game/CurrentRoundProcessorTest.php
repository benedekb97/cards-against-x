<?php

declare(strict_types=1);

namespace Unit\Processor\Game;

use App\Entity\Game;
use App\Entity\Round;
use App\Entity\Turn;
use App\Processor\Game\CurrentRoundProcessor;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class CurrentRoundProcessorTest extends TestCase
{
    private CurrentRoundProcessor $currentRoundProcessor;

    public function setUp(): void
    {
        $this->currentRoundProcessor = new CurrentRoundProcessor(
            $this->createMock(EntityManagerInterface::class)
        );
    }

    public function testNoCurrentRound(): void
    {
        $this->expectNotToPerformAssertions();

        $game = new Game();

        $this->currentRoundProcessor->process($game);
    }

    public function testNoNewRoundNecessary(): void
    {
        $game = new Game();
        $roundOne = new Round();
        $roundTwo = new Round();

        $roundOne->setNumber(1);
        $roundTwo->setNumber(2);

        $game->addRound($roundOne);
        $game->addRound($roundTwo);
        $game->setCurrentRound($roundOne);

        $turn = new Turn();

        $roundOne->addTurn($turn);
        $roundOne->setCurrentTurn($turn);

        $this->currentRoundProcessor->process($game);

        $this->assertEquals($roundOne, $game->getCurrentRound());
    }

    public function testNewRoundNecessary(): void
    {
        $game = new Game();

        $roundOne = new Round();
        $roundTwo = new Round();

        $roundOne->setNumber(1);
        $roundTwo->setNumber(2);

        $game->addRound($roundTwo);
        $game->addRound($roundOne);
        $game->setCurrentRound($roundOne);

        $this->currentRoundProcessor->process($game);

        $this->assertEquals($roundTwo, $game->getCurrentRound());
    }

    public function testLastRound(): void
    {
        $game = new Game();

        $roundOne = new Round();
        $roundTwo = new Round();

        $roundOne->setNumber(1);
        $roundTwo->setNumber(2);

        $game->addRound($roundOne);
        $game->addRound($roundTwo);
        $game->setCurrentRound($roundTwo);

        $this->currentRoundProcessor->process($game);

        $this->assertNull($game->getCurrentRound());
    }
}