<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Traits\DeletableTrait;
use App\Entity\Traits\HasGameTrait;
use App\Entity\Traits\ResourceTrait;
use App\Entity\Traits\TimestampableTrait;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\ORM\Mapping\OneToOne;

#[Entity]
#[HasLifecycleCallbacks]
class Round implements RoundInterface
{
    use ResourceTrait;
    use HasGameTrait;
    use TimestampableTrait;
    use DeletableTrait;

    #[OneToOne(targetEntity: Turn::class)]
    private ?TurnInterface $currentTurn = null;

    #[Column(type: Types::INTEGER, nullable: true)]
    private ?int $number = null;

    public function getCurrentTurn(): ?TurnInterface
    {
        return $this->currentTurn;
    }

    public function setCurrentTurn(?TurnInterface $currentTurn): void
    {
        $this->currentTurn = $currentTurn;
    }

    public function getNumber(): ?int
    {
        return $this->number;
    }

    public function setNumber(?int $number): void
    {
        $this->number = $number;
    }
}