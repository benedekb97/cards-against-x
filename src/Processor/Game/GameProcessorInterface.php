<?php

declare(strict_types=1);

namespace App\Processor\Game;

use App\Entity\GameInterface;

interface GameProcessorInterface
{
    public const SERVICE_TAG = 'app.game_processor';

    public function process(GameInterface $game): void;
}