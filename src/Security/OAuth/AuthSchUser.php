<?php

declare(strict_types=1);

namespace App\Security\OAuth;

class AuthSchUser
{
    public function __construct(
        private readonly string $internalId,
        private readonly string $displayName,
        private readonly string $email
    ) {}

    public function getInternalId(): string
    {
        return $this->internalId;
    }

    public function getDisplayName(): string
    {
        return $this->displayName;
    }

    public function getEmail(): string
    {
        return $this->email;
    }
}