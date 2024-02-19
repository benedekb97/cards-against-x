<?php

declare(strict_types=1);

namespace App\Processor\Game;

use App\Entity\GameInterface;

class GameProcessor implements GameProcessorInterface
{
    private array $processors = [];

    public function addProcessor(int $priority, GameProcessorInterface $processor): void
    {
        $this->processors[$priority] = $processor;

        ksort($this->processors);
    }

    public function process(GameInterface $game): void
    {
        /** @var GameProcessorInterface $processor */
        foreach ($this->processors as $processor) {
            $processor->process($game);
        }
    }
}