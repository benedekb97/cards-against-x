<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Entity\PlayerInterface;
use App\Entity\UserInterface;
use App\Event\LobbyUpdateEvent;
use App\Message\LobbyMessage;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\SerializerInterface;

#[AsEventListener(event: LobbyUpdateEvent::class, method: 'onLobbyUpdate')]
readonly class LobbyEventListener
{
    public function __construct(
        private HubInterface        $hub,
        private SerializerInterface $serializer,
        private UrlGeneratorInterface $urlGenerator
    ) {}

    public function onLobbyUpdate(LobbyUpdateEvent $event): void
    {
        $game = $event->getGame();

        $lobbyUpdateMessage = new LobbyMessage(
            $url = $this->urlGenerator->generate('lobby', ['slug' => $game->getSlug()]),
            $game->getNumberOfRounds(),
            $game->getDeck()?->getId(),
            $game->getPlayers()->map(
                static function (PlayerInterface $player): UserInterface
                {
                    return $player->getUser();
                }
            )->toArray(),
            $game->isReadyToStart()
        );

        $this->hub->publish(
            new Update(
                'http://localhost' . $url,
                $this->serializer->serialize($lobbyUpdateMessage, 'json', ['groups' => ['lobbyUpdate']])
            )
        );
    }
}