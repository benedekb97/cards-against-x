<?php

declare(strict_types=1);

namespace App\Message;

interface MessageInterface
{
    public function getUrl(): string;
}