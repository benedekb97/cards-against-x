<?php

declare(strict_types=1);

namespace App\Message;

class EndTurnMessage
{
    public function __construct(
        private int $turnId
    ) {}

    public function setTurnId(int $turnId): void
    {
        $this->turnId = $turnId;
    }

    public function getTurnId(): int
    {
        return $this->turnId;
    }
}