<?php

declare(strict_types=1);

namespace Unit\EventListener;

use App\Entity\Game;
use App\Entity\Player;
use App\Entity\Round;
use App\Entity\Turn;
use App\Event\GameUpdateEvent;
use App\EventListener\GameEventListener;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\SerializerInterface;

class GameEventListenerTest extends TestCase
{
    public function testOnGameUpdate(): void
    {
        $this->expectNotToPerformAssertions();

        $serializer = $this->createMock(SerializerInterface::class);
        $serializer->method('serialize')->willReturn('random_string');

        $urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $urlGenerator->method('generate')->willReturn('/random/uri');

        $gameEventListener = new GameEventListener(
            $this->createMock(HubInterface::class),
            $serializer,
            $urlGenerator
        );

        $game = new Game();

        $game->addPlayer(new Player());
        $round = new Round();
        $turn = new Turn();

        $game->addRound($round);
        $game->setCurrentRound($round);

        $round->addTurn($turn);
        $round->setCurrentTurn($turn);

        $event = new GameUpdateEvent($game);

        $gameEventListener->onGameUpdate($event);
    }
}