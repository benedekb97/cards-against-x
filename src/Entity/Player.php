<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Enum\TurnStatus;
use App\Entity\Traits\DeletableTrait;
use App\Entity\Traits\HasGameTrait;
use App\Entity\Traits\HasUserTrait;
use App\Entity\Traits\ResourceTrait;
use App\Entity\Traits\TimestampableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\ORM\Mapping\ManyToMany;
use Doctrine\ORM\Mapping\OneToMany;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;
use function Symfony\Component\String\b;

#[Entity]
#[HasLifecycleCallbacks]
class Player implements PlayerInterface
{
    use ResourceTrait;
    use HasUserTrait;
    use DeletableTrait;
    use TimestampableTrait;
    use HasGameTrait;

    public function __construct(
        #[Column(type: Types::BOOLEAN)]
        #[Groups(['lobbyUpdate', 'gameUpdate'])]
        private bool $ready = false,

        #[Groups(['gameUpdate'])]
        #[Column(type: Types::INTEGER)]
        private int $votes = 0,

        #[Groups(['gameUpdate'])]
        #[Column(type: Types::BOOLEAN)]
        private bool $voted = false,

        #[ManyToMany(targetEntity: Card::class, cascade: ['all'])]
        private Collection $cards = new ArrayCollection(),

        #[OneToMany(mappedBy: 'player', targetEntity: Play::class)]
        private Collection $plays = new ArrayCollection()
    ) {}

    public function isReady(): bool
    {
        return $this->ready;
    }

    public function setReady(bool $ready): void
    {
        $this->ready = $ready;
    }

    public function getVotes(): int
    {
        return $this->votes;
    }

    public function setVotes(int $votes): void
    {
        $this->votes = $votes;
    }

    public function isVoted(): bool
    {
        return $this->voted;
    }

    public function setVoted(bool $voted): void
    {
        $this->voted = $voted;
    }

    public function getCards(): Collection
    {
        return $this->cards;
    }

    public function hasCard(CardInterface $card): bool
    {
        return $this->cards->contains($card);
    }

    public function addCard(CardInterface $card): void
    {
        if (!$this->hasCard($card)) {
            $this->cards->add($card);
        }
    }

    public function removeCard(CardInterface $card): void
    {
        if ($this->hasCard($card)) {
            $this->cards->removeElement($card);
        }
    }

    #[Groups(['lobbyUpdate'])]
    #[SerializedName('host')]
    public function isHost(): bool
    {
        return $this->user === $this->game->getCreatedBy();
    }

    #[Groups(['gameUpdate'])]
    #[SerializedName('name')]
    public function getName(): string
    {
        return $this->user->getNickname() ?? $this->user->getName() ?? '';
    }

    #[Groups(['gameUpdate'])]
    #[SerializedName('played')]
    public function hasPlayed(): bool
    {
        return ($this->getGame()?->getCurrentRound()?->getCurrentTurn()?->hasPlayerPlayed($this) ?? false) &&
            ($this->getGame()?->getCurrentRound()?->getCurrentTurn()?->getStatus() === TurnStatus::IN_PROGRESS);
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
            $play->setPlayer($this);
        }
    }

    public function removePlay(PlayInterface $play): void
    {
        if ($this->hasPlay($play)) {
            $this->plays->removeElement($play);
            $play->setPlayer(null);
        }
    }

    #[Groups(['gameUpdate'])]
    #[SerializedName('points')]
    public function getPoints(): int
    {
        $points = 0;

        /** @var PlayInterface $play */
        foreach ($this->plays as $play) {
            $points += $play->getPoints();
        }

        return $points;
    }
}