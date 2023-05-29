<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Traits\DeletableInterface;
use App\Entity\Traits\ResourceInterface;
use App\Entity\Traits\TimestampableInterface;

interface UserInterface extends ResourceInterface, TimestampableInterface, DeletableInterface
{
    public function getName(): ?string;

    public function setName(?string $name): void;

    public function getEmail(): ?string;

    public function setEmail(?string $email): void;

    public function getInternalId(): ?string;

    public function setInternalId(?string $internalId): void;

    public function getRememberToken(): ?string;

    public function setRememberToken(?string $rememberToken): void;

    public function getNickname(): ?string;

    public function setNickname(?string $nickname): void;

    public function getPassword(): ?string;

    public function setPassword(?string $password): void;

    public function isActivated(): bool;

    public function setActivated(bool $activated): void;
}