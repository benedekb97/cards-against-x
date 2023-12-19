<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Enum\CardType;
use App\Entity\Traits\CreatedByUserTrait;
use App\Entity\Traits\DeletableTrait;
use App\Entity\Traits\ResourceTrait;
use App\Entity\Traits\TimestampableTrait;
use App\Repository\DeckRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\ORM\Mapping\InverseJoinColumn;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\JoinTable;
use Doctrine\ORM\Mapping\ManyToMany;

#[Entity(repositoryClass: DeckRepository::class)]
#[HasLifecycleCallbacks]
class Deck implements DeckInterface
{
    use ResourceTrait;
    use CreatedByUserTrait;
    use TimestampableTrait;
    use DeletableTrait;

    public function __construct(
        #[Column(type: Types::STRING, nullable: true)]
        private ?string $name = null,

        #[Column(type: Types::BOOLEAN)]
        private bool $public = false,

        #[ManyToMany(targetEntity: Card::class, mappedBy: 'decks', cascade: ['persist'])]
        private Collection $cards = new ArrayCollection()
    ) {}

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function isPublic(): bool
    {
        return $this->public;
    }

    public function setPublic(bool $public): void
    {
        $this->public = $public;
    }

    public function getWhiteCards(): Collection
    {
        return $this->cards->filter(
            static function (CardInterface $card): bool
            {
                return $card->getType() === CardType::WHITE;
            }
        );
    }

    public function getBlackCards(): Collection
    {
        return $this->cards->filter(
            static function (CardInterface $card): bool
            {
                return $card->getType() === CardType::BLACK;
            }
        );
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
            $card->addDeck($this);
        }
    }

    public function removeCard(CardInterface $card): void
    {
        if ($this->hasCard($card)) {
            $this->cards->removeElement($card);
            $card->removeDeck($this);
        }
    }
}