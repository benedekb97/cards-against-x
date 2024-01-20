<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Event\GameUpdateEvent;
use App\Message\GameMessage;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\SerializerInterface;

#[AsEventListener(event: GameUpdateEvent::class, method: 'onGameUpdate')]
readonly class GameEventListener
{
    public function __construct(
        private HubInterface $hub,
        private SerializerInterface $serializer,
        private UrlGeneratorInterface $urlGenerator
    ) {}

    public function onGameUpdate(GameUpdateEvent $event): void
    {
        $game = $event->getGame();

        $gameUpdateMessage = new GameMessage(
            $url = $this->urlGenerator->generate('game', ['slug' => $game->getSlug()]),
            $game->getPlayers()->toArray(),
            $game->getStatus(),
            $game?->getCurrentRound()?->getCurrentTurn()?->getStatus()
        );

        $this->hub->publish(
            new Update(
                'http://localhost' . $url,
                $this->serializer->serialize($gameUpdateMessage, 'json', ['groups' => ['gameUpdate']])
            )
        );
    }
}