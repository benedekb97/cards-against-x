<?php

declare(strict_types=1);

namespace App\Entity\Traits;

use App\Entity\UserInterface;

interface HasUserInterface
{
    public function getUser(): ?UserInterface;

    public function setUser(?UserInterface $user): void;
}