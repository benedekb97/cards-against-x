<?php

declare(strict_types=1);

namespace App\Message;

use Symfony\Component\Serializer\Annotation\Groups;

abstract class AbstractMessage implements MessageInterface
{
    public function __construct(
        #[Groups(['lobbyUpdate', 'gameUpdate'])]
        private readonly string $url
    ) {}

    public function getUrl(): string
    {
        return $this->url;
    }
}