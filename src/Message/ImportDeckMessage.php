<?php

declare(strict_types=1);

namespace App\Message;

use App\Entity\UserInterface;

readonly class ImportDeckMessage
{
    public function __construct(
        private string $fileLocation,
        private int $userId,
    ) {}

    public function getFileLocation(): string
    {
        return $this->fileLocation;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }
}