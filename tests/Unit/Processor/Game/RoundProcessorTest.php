<?php

declare(strict_types=1);

namespace Unit\Processor\Game;

use App\Entity\Enum\TurnStatus;
use App\Entity\Game;
use App\Entity\Round;
use App\Entity\Turn;
use App\Processor\Game\RoundProcessor;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class RoundProcessorTest extends TestCase
{
    private RoundProcessor $roundProcessor;

    public function setUp(): void
    {
        $this->roundProcessor = new RoundProcessor(
            $this->createMock(EntityManagerInterface::class)
        );
    }

    public function testNoRound(): void
    {
        $this->expectNotToPerformAssertions();

        $game = new Game();

        $this->roundProcessor->process($game);
    }

    public function testNoTurn(): void
    {
        $this->expectNotToPerformAssertions();

        $game = new Game();
        $round = new Round();

        $game->addRound($round);
        $game->setCurrentRound($round);

        $this->roundProcessor->process($game);
    }

    /**
     * @dataProvider turnStatusDataProvider
     * @param TurnStatus $status
     * @return void
     */
    public function testTurnStatuses(TurnStatus $status): void
    {
        $game = new Game();
        $round = new Round();
        $turn = new Turn(status: $status, number: 1);
        $turnTwo = new Turn(number: 2);

        $game->addRound($round);
        $game->setCurrentRound($round);
        $round->addTurn($turn);
        $round->addTurn($turnTwo);
        $round->setCurrentTurn($turn);

        $this->roundProcessor->process($game);

        $this->assertEquals($turn, $round->getCurrentTurn());
    }

    public function testTurnFinished(): void
    {
        $game = new Game();
        $round = new Round();
        $turn = new Turn(status: TurnStatus::FINISHED, number: 1);
        $turnTwo = new Turn(status: TurnStatus::DRAFT, number: 2);

        $game->addRound($round);
        $game->setCurrentRound($round);

        $round->addTurn($turn);
        $round->addTurn($turnTwo);
        $round->setCurrentTurn($turn);

        $this->roundProcessor->process($game);

        $this->assertEquals($turnTwo, $round->getCurrentTurn());
    }

    private function turnStatusDataProvider(): array
    {
        return [
            TurnStatus::DRAFT->name => [TurnStatus::DRAFT],
            TurnStatus::IN_PROGRESS->name => [TurnStatus::IN_PROGRESS],
            TurnStatus::CHOOSING->name => [TurnStatus::CHOOSING],
            TurnStatus::RECAP->name => [TurnStatus::RECAP],
        ];
    }
}