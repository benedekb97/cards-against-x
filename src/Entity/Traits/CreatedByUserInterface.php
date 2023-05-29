<?php

declare(strict_types=1);

namespace App\Entity\Traits;

use App\Entity\UserInterface;

interface CreatedByUserInterface
{
    public function getCreatedBy(): ?UserInterface;

    public function setCreatedBy(?UserInterface $user): void;
}