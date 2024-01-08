<?php

declare(strict_types=1);

namespace Unit\Processor\Game;

use App\Entity\Game;
use App\Processor\Game\GamePlayerCardProcessor;
use App\Service\PlayerCardServiceInterface;
use PHPUnit\Framework\TestCase;

class GamePlayerCardProcessorTest extends TestCase
{
    private GamePlayerCardProcessor $gamePlayerCardProcessor;

    public function setUp(): void
    {
        $this->gamePlayerCardProcessor = new GamePlayerCardProcessor(
            $this->createMock(PlayerCardServiceInterface::class)
        );
    }

    public function testNoTurnSet(): void
    {
        $this->expectNotToPerformAssertions();

        $game = new Game();

        $this->gamePlayerCardProcessor->process($game);
    }
}