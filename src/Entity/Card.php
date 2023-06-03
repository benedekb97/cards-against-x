<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Enum\CardType;
use App\Entity\Traits\CreatedByUserTrait;
use App\Entity\Traits\DeletableTrait;
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
class Card implements CardInterface
{
    use ResourceTrait;
    use TimestampableTrait;
    use DeletableTrait;
    use CreatedByUserTrait;

    public function __construct(
        #[Column(type: Types::STRING, enumType: CardType::class)]
        private ?CardType $type = null,

        #[Column(type: Types::JSON)]
        private ?array $text = null,

        #[ManyToMany(targetEntity: Deck::class, inversedBy: 'cards')]
        private readonly Collection $decks = new ArrayCollection()
    ) {}

    public function getType(): ?CardType
    {
        return $this->type;
    }

    public function setType(?CardType $type): void
    {
        $this->type = $type;
    }

    public function getText(): ?array
    {
        return $this->text;
    }

    public function getFormattedText(): string
    {
        return implode('_____', $this->text);
    }

    public function setText(?array $text): void
    {
        $this->text = $text;
    }

    public function getDecks(): Collection
    {
        return $this->decks;
    }

    public function hasDeck(DeckInterface $deck): bool
    {
        return $this->decks->contains($deck);
    }

    public function addDeck(DeckInterface $deck): void
    {
        if (!$this->hasDeck($deck)) {
            $this->decks->add($deck);
            $deck->addCard($this);
        }
    }

    public function removeDeck(DeckInterface $deck): void
    {
        if ($this->hasDeck($deck)) {
            $this->decks->removeElement($deck);
            $deck->removeCard($this);
        }
    }

    public function getBlankCount(): int
    {
        if ($this->type === CardType::WHITE) {
            return 0;
        }

        return count($this->text) - 1;
    }
}