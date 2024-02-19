<?php

declare(strict_types=1);

namespace App\Tests\Unit\EventListener;

use App\Entity\Game;
use App\Event\LobbyUpdateEvent;
use App\EventListener\LobbyEventListener;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\SerializerInterface;

class LobbyEventListenerTest extends TestCase
{
    public function testOnLobbyUpdate(): void
    {
        $this->expectNotToPerformAssertions();

        $hub = $this->createMock(HubInterface::class);

        $serializer = $this->createMock(SerializerInterface::class);
        $serializer->method('serialize')->willReturn('random_string');

        $urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $urlGenerator->method('generate')->willReturn('/random/uri');

        $parameterBag = $this->createMock(ParameterBagInterface::class);
        $parameterBag->method( 'get')->willReturn('http://localhost');

        $lobbyEventListener = new LobbyEventListener(
            $hub,
            $serializer,
            $urlGenerator,
            $parameterBag
        );

        $lobbyEventListener->onLobbyUpdate(new LobbyUpdateEvent(new Game()));
    }
}