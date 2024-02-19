<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Traits\DeletableTrait;
use App\Entity\Traits\HasGameTrait;
use App\Entity\Traits\ResourceTrait;
use App\Entity\Traits\TimestampableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\OneToOne;
use function Symfony\Component\String\b;

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

    public function __construct(
        #[OneToMany(mappedBy: 'round', targetEntity: Turn::class, cascade: ['persist'])]
        private Collection $turns = new ArrayCollection()
    ) {}

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

    public function getTurns(): Collection
    {
        return $this->turns;
    }

    public function hasTurn(TurnInterface $turn): bool
    {
        return $this->turns->contains($turn);
    }

    public function addTurn(TurnInterface $turn): void
    {
        if (!$this->hasTurn($turn)) {
            $this->turns->add($turn);
            $turn->setRound($this);
        }
    }

    public function removeTurn(TurnInterface $turn): void
    {
        if ($this->hasTurn($turn)) {
            $this->turns->removeElement($turn);
            $turn->setRound(null);
        }
    }

    public function getTurn(int $number): ?TurnInterface
    {
        /** @var TurnInterface $turn */
        foreach ($this->turns as $turn) {
            if ($turn->getNumber() === $number) {
                return $turn;
            }
        }

        return null;
    }
}