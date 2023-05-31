<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Traits\DeletableTrait;
use App\Entity\Traits\ResourceTrait;
use App\Entity\Traits\TimestampableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\OneToOne;

#[Entity]
#[HasLifecycleCallbacks]
class Turn implements TurnInterface
{
    use ResourceTrait;
    use DeletableTrait;
    use TimestampableTrait;

    public function __construct(
        #[ManyToOne(targetEntity: Player::class)]
        private ?PlayerInterface $player = null,

        #[ManyToOne(targetEntity: Card::class)]
        private ?CardInterface $card = null,

        #[ManyToOne(targetEntity: Round::class)]
        private ?RoundInterface $round = null,

        #[OneToOne(targetEntity: Play::class)]
        private ?PlayInterface $winningPlay = null,

        #[OneToMany(mappedBy: 'turn', targetEntity: Play::class)]
        private Collection $plays = new ArrayCollection()
    ) {}

    public function getPlayer(): ?PlayerInterface
    {
        return $this->player;
    }

    public function setPlayer(?PlayerInterface $player): void
    {
        $this->player = $player;
    }

    public function getCard(): ?CardInterface
    {
        return $this->card;
    }

    public function setCard(?CardInterface $card): void
    {
        $this->card = $card;
    }

    public function getRound(): ?RoundInterface
    {
        return $this->round;
    }

    public function setRound(?RoundInterface $round): void
    {
        $this->round = $round;
    }

    public function getWinningPlay(): ?PlayInterface
    {
        return $this->winningPlay;
    }

    public function setWinningPlay(?PlayInterface $winningPlay): void
    {
        $this->winningPlay = $winningPlay;
    }

    public function getPlays(): Collection
    {
        return $this->plays;
    }

    public function hasPlay(PlayInterface $play): bool
    {
        return $this->plays->contains($play);
    }

    public function addPlay(PlayInterface $play): void
    {
        if (!$this->hasPlay($play)) {
            $this->plays->add($play);
            $play->setTurn($this);
        }
    }

    public function removePlay(PlayInterface $play): void
    {
        if ($this->hasPlay($play)) {
            $this->plays->removeElement($play);
            $play->setTurn(null);
        }
    }
}