<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Enum\TurnStatus;
use App\Entity\Traits\DeletableTrait;
use App\Entity\Traits\ResourceTrait;
use App\Entity\Traits\TimestampableTrait;
use App\Repository\TurnRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\OneToOne;

#[Entity(repositoryClass: TurnRepository::class)]
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

        #[ManyToOne(targetEntity: Round::class, cascade: ['persist'], inversedBy: 'turns')]
        private ?RoundInterface $round = null,

        #[OneToOne(targetEntity: Play::class)]
        private ?PlayInterface $winningPlay = null,

        #[OneToMany(mappedBy: 'turn', targetEntity: Play::class)]
        private Collection $plays = new ArrayCollection(),

        #[Column(type: Types::STRING, enumType: TurnStatus::class)]
        private TurnStatus $status = TurnStatus::DRAFT,

        #[Column(type: Types::INTEGER, nullable: true)]
        private ?int $number = null
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

    public function getStatus(): TurnStatus
    {
        return $this->status;
    }

    public function setStatus(TurnStatus $status): void
    {
        $this->status = $status;
    }

    public function getNumber(): ?int
    {
        return $this->number;
    }

    public function setNumber(?int $number): void
    {
        $this->number = $number;
    }

    public function hasPlayerPlayed(PlayerInterface $player): bool
    {
        return !$this->plays->filter(
            static function (PlayInterface $play) use ($player): bool
            {
                return $play->getPlayer() === $player;
            }
        )->isEmpty();
    }
}