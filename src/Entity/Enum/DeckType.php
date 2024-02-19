<?php

declare(strict_types=1);

namespace App\Entity\Enum;

enum DeckType: string
{
    case DRAFT = 'draft';
    case PUBLIC = 'public';
    case PRIVATE = 'private';

    public function getName(): string
    {
        return match ($this) {
            self::DRAFT => 'Draft',
            self::PUBLIC => 'Public',
            self::PRIVATE => 'Private',
        };
    }
}