<?php

declare(strict_types=1);

namespace App\Event;

use App\Entity\RoundInterface;
use Symfony\Contracts\EventDispatcher\Event;

class RoundEvent extends Event
{
    public function __construct(
        private readonly RoundInterface $round
    ) {}

    public function getRound(): RoundInterface
    {
        return $this->round;
    }
}