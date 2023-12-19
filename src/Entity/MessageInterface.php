<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Traits\CreatedByUserInterface;
use App\Entity\Traits\DeletableInterface;
use App\Entity\Traits\HasGameInterface;
use App\Entity\Traits\ResourceInterface;
use App\Entity\Traits\TimestampableInterface;

interface MessageInterface extends
    ResourceInterface,
    CreatedByUserInterface,
    DeletableInterface,
    TimestampableInterface,
    HasGameInterface
{
    public function getMessage(): ?string;

    public function setMessage(?string $message): void;

    public function getGame(): ?GameInterface;

    public function setGame(?GameInterface $game): void;
}