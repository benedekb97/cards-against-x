<?php

declare(strict_types=1);

namespace App\Entity;

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
        private bool $ready = false,

        #[Column(type: Types::INTEGER)]
        private int $votes = 0,

        #[Column(type: Types::BOOLEAN)]
        private bool $voted = false,

        #[ManyToMany(targetEntity: Card::class)]
        private Collection $cards = new ArrayCollection()
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
}