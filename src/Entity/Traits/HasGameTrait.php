<?php

declare(strict_types=1);

namespace App\Entity\Traits;

use App\Entity\Game;
use App\Entity\GameInterface;
use Doctrine\ORM\Mapping\ManyToOne;

trait HasGameTrait
{
    #[ManyToOne(targetEntity: Game::class)]
    private ?GameInterface $game = null;

    public function getGame(): ?GameInterface
    {
        return $this->game;
    }

    public function setGame(?GameInterface $game): void
    {
        $this->game = $game;
    }
}