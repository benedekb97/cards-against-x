<?php

declare(strict_types=1);

namespace App\Entity\Traits;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Symfony\Component\Serializer\Annotation\Groups;

trait ResourceTrait
{
    #[Id]
    #[GeneratedValue]
    #[Column(type: Types::BIGINT)]
    #[Groups(['lobbyUpdate', 'gameUpdate'])]
    private ?int $id = null;

    public function getId(): ?int
    {
        return $this->id;
    }
}