<?php

declare(strict_types=1);

namespace App\Entity\Traits;

use App\Entity\User;
use App\Entity\UserInterface;
use Doctrine\ORM\Mapping\ManyToOne;

trait HasUserTrait
{
    #[ManyToOne(targetEntity: User::class, fetch: 'EAGER')]
    private ?UserInterface $user = null;

    public function getUser(): ?UserInterface
    {
        return $this->user;
    }

    public function setUser(?UserInterface $user): void
    {
        $this->user = $user;
    }
}