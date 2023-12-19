<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Traits\DeletableTrait;
use App\Entity\Traits\ResourceTrait;
use App\Entity\Traits\TimestampableTrait;
use App\Repository\PlayRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\ORM\Mapping\ManyToMany;
use Doctrine\ORM\Mapping\ManyToOne;

#[Entity(repositoryClass: PlayRepository::class)]
#[HasLifecycleCallbacks]
class Play implements PlayInterface
{
    use ResourceTrait;
    use DeletableTrait;
    use TimestampableTrait;

    public function __construct(
        #[ManyToOne(targetEntity: Player::class, inversedBy: 'plays')]
        private ?PlayerInterface $player = null,

        #[ManyToOne(targetEntity: Turn::class, inversedBy: 'plays')]
        private ?TurnInterface $turn = null,

        #[Column(type: Types::INTEGER, nullable: true)]
        private ?int $points = null,

        #[Column(type: Types::INTEGER)]
        private int $likes = 0,

        #[Column(type: Types::BOOLEAN)]
        private bool $featured = false,

        #[ManyToMany(targetEntity: Card::class)]
        private Collection $cards = new ArrayCollection()
    ) {}

    public function getPlayer(): ?PlayerInterface
    {
        return $this->player;
    }

    public function setPlayer(?PlayerInterface $player): void
    {
        $this->player = $player;
    }

    public function getTurn(): ?TurnInterface
    {
        return $this->turn;
    }

    public function setTurn(?TurnInterface $turn): void
    {
        $this->turn = $turn;
    }

    public function getPoints(): ?int
    {
        return $this->points;
    }

    public function setPoints(?int $points): void
    {
        $this->points = $points;
    }

    public function getLikes(): int
    {
        return $this->likes;
    }

    public function setLikes(int $likes): void
    {
        $this->likes = $likes;
    }

    public function isFeatured(): bool
    {
        return $this->featured;
    }

    public function setFeatured(bool $featured): void
    {
        $this->featured = $featured;
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

    public function getPlayHTML(): string
    {
        $blackText = $this->getTurn()->getCard()->getText();

        $play = $blackText[0];

        /**
         * @var int $key
         * @var CardInterface $card
         */
        foreach ($this->getCards() as $key => $card) {
            $play .= sprintf(
                ' <span class="white-text">%s</span> %s',
                implode('', $card->getText()),
                $blackText[$key+1]
            );
        }

        return $play;
    }

    public function isWinningPlay(): bool
    {
        return $this->turn->getWinningPlay() === $this;
    }
}