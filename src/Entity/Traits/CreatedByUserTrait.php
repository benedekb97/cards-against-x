<?php

declare(strict_types=1);

namespace App\Entity\Traits;

use App\Entity\User;
use App\Entity\UserInterface;
use Doctrine\ORM\Mapping\ManyToOne;

trait CreatedByUserTrait
{
    #[ManyToOne(targetEntity: User::class)]
    private ?UserInterface $createdBy = null;

    public function getCreatedBy(): ?UserInterface
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?UserInterface $user): void
    {
        $this->createdBy = $user;
    }
}