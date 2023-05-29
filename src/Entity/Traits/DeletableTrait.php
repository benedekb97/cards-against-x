<?php

declare(strict_types=1);

namespace App\Entity\Traits;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;

trait DeletableTrait
{
    #[Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $deletedAt = null;

    public function getDeletedAt(): ?\DateTimeInterface
    {
        return $this->deletedAt;
    }

    public function delete(): void
    {
        $this->deletedAt = new \DateTime();
    }

    public function restore(): void
    {
        $this->deletedAt = null;
    }
}