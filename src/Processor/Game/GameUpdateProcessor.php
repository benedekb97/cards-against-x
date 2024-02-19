<?php

declare(strict_types=1);

namespace App\Processor\Game;

use App\Entity\GameInterface;
use App\Event\GameUpdateEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

readonly class GameUpdateProcessor implements GameProcessorInterface
{
    public function __construct(
        private EventDispatcherInterface $eventDispatcher
    ) {}

    public function process(GameInterface $game): void
    {
        $this->eventDispatcher->dispatch(new GameUpdateEvent($game));
    }
}