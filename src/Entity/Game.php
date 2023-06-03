<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Enum\GameStatus;
use App\Entity\Traits\CreatedByUserTrait;
use App\Entity\Traits\DeletableTrait;
use App\Entity\Traits\ResourceTrait;
use App\Entity\Traits\TimestampableTrait;
use App\Repository\GameRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\OneToOne;
use Symfony\Component\Serializer\Annotation\Groups;

#[Entity(repositoryClass: GameRepository::class)]
#[HasLifecycleCallbacks]
class Game implements GameInterface
{
    use ResourceTrait;
    use TimestampableTrait;
    use DeletableTrait;
    use CreatedByUserTrait;

    public function __construct(
        #[Column(type: Types::STRING, unique: true)]
        private ?string $slug = null,

        #[Column(type: Types::INTEGER)]
        private int $numberOfRounds = self::DEFAULT_ROUND_COUNT,

        #[Column(type: Types::STRING, enumType: GameStatus::class)]
        private GameStatus $status = GameStatus::LOBBY,

        #[OneToOne(targetEntity: Round::class)]
        private ?RoundInterface $currentRound = null,

        #[ManyToOne(targetEntity: Deck::class)]
        private ?DeckInterface $deck = null,

        #[Column(type: Types::BOOLEAN)]
        private bool $spectatable = false,

        #[OneToMany(mappedBy: 'game', targetEntity: Message::class)]
        private Collection $messages = new ArrayCollection(),

        #[OneToMany(mappedBy: 'game', targetEntity: Player::class, cascade: ['persist'], fetch: 'EAGER')]
        private Collection $players = new ArrayCollection(),

        #[OneToMany(mappedBy: 'game', targetEntity: Round::class, cascade: ['persist'])]
        private Collection $rounds = new ArrayCollection()
    ) {}

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): void
    {
        $this->slug = $slug;
    }

    public function getNumberOfRounds(): ?int
    {
        return $this->numberOfRounds;
    }

    public function setNumberOfRounds(?int $numberOfRounds): void
    {
        $this->numberOfRounds = $numberOfRounds;
    }

    public function getStatus(): GameStatus
    {
        return $this->status;
    }

    public function setStatus(GameStatus $status): void
    {
        $this->status = $status;
    }

    public function getCurrentRound(): ?RoundInterface
    {
        return $this->currentRound;
    }

    public function setCurrentRound(?RoundInterface $currentRound): void
    {
        $this->currentRound = $currentRound;
    }

    public function getDeck(): ?DeckInterface
    {
        return $this->deck;
    }

    public function setDeck(?DeckInterface $deck): void
    {
        $this->deck = $deck;
    }

    public function isSpectatable(): bool
    {
        return $this->spectatable;
    }

    public function setSpectatable(bool $spectatable): void
    {
        $this->spectatable = $spectatable;
    }

    public function getMessages(): Collection
    {
        return $this->messages;
    }

    public function hasMessage(MessageInterface $message): bool
    {
        return $this->messages->contains($message);
    }

    public function addMessage(MessageInterface $message): void
    {
        if (!$this->hasMessage($message)) {
            $this->messages->add($message);
            $message->setGame($this);
        }
    }

    public function removeMessage(MessageInterface $message): void
    {
        if ($this->hasMessage($message)) {
            $this->messages->removeElement($message);
            $message->setGame(null);
        }
    }

    public function getPlayers(): Collection
    {
        return $this->players;
    }

    public function hasPlayer(PlayerInterface $player): bool
    {
        return $this->players->contains($player);
    }

    public function addPlayer(PlayerInterface $player): void
    {
        if (!$this->hasPlayer($player)) {
            $this->players->add($player);
            $player->setGame($this);
        }
    }

    public function removePlayer(PlayerInterface $player): void
    {
        if ($this->hasPlayer($player)) {
            $this->players->removeElement($player);
            $player->setGame(null);
        }
    }

    public function getRounds(): Collection
    {
        return $this->rounds;
    }

    public function hasRound(RoundInterface $round): bool
    {
        return $this->rounds->contains($round);
    }

    public function addRound(RoundInterface $round): void
    {
        if (!$this->hasRound($round)) {
            $this->rounds->add($round);
            $round->setGame($this);
        }
    }

    public function removeRound(RoundInterface $round): void
    {
        if ($this->hasRound($round)) {
            $this->rounds->removeElement($round);
            $round->setGame(null);
        }
    }

    public function isReadyToStart(): bool
    {
        return $this->status === GameStatus::LOBBY &&
            $this->players->filter(static fn (PlayerInterface $player) => !$player->isReady())->isEmpty() &&
            $this->players->count() > 1 &&
            $this->deck !== null;
    }
}