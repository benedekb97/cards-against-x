<?php

declare(strict_types=1);

namespace App\Entity\Enum;

enum ImportStatus: string
{
    case DRAFT = 'draft';
    case IN_PROGRESS = 'in_progress';
    case IMPORTED = 'imported';
    case ERROR = 'error';

    public function getName(): string
    {
        return match($this) {
            self::DRAFT => 'Draft',
            self::IN_PROGRESS => 'In progress',
            self::IMPORTED => 'Imported',
            self::ERROR => 'Error',
        };
    }
}