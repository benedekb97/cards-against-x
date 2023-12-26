<?php

declare(strict_types=1);

namespace App\Message;

class ImportCompletedMessage extends AbstractMessage
{
    public function __construct(
        string $url,
        private readonly int $deckId
    ){
        parent::__construct($url);
    }

    public function getDeckId(): int
    {
        return $this->deckId;
    }
}