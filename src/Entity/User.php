<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Traits\DeletableTrait;
use App\Entity\Traits\ResourceTrait;
use App\Entity\Traits\TimestampableTrait;
use App\Repository\UserRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;

#[Entity(repositoryClass: UserRepository::class)]
#[HasLifecycleCallbacks]
class User implements UserInterface
{
    use ResourceTrait;
    use TimestampableTrait;
    use DeletableTrait;

    #[Column(type: Types::STRING, nullable: true)]
    private ?string $name = null;

    #[Column(type: Types::STRING, nullable: true)]
    private ?string $email = null;

    #[Column(type: Types::STRING, nullable: true)]
    private ?string $internalId = null;

    #[Column(type: Types::STRING, nullable: true)]
    private ?string $rememberToken = null;

    #[Column(type: Types::STRING, nullable: true)]
    private ?string $nickname = null;

    #[Column(type: Types::STRING, nullable: true)]
    private ?string $password = null;

    #[Column(type: Types::BOOLEAN)]
    private bool $activated = true; // TODO: Set up emailing to enable activation.

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    public function getInternalId(): ?string
    {
        return $this->internalId;
    }

    public function setInternalId(?string $internalId): void
    {
        $this->internalId = $internalId;
    }

    public function getRememberToken(): ?string
    {
        return $this->rememberToken;
    }

    public function setRememberToken(?string $rememberToken): void
    {
        $this->rememberToken = $rememberToken;
    }

    public function getNickname(): ?string
    {
        return $this->nickname;
    }

    public function setNickname(?string $nickname): void
    {
        $this->nickname = $nickname;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): void
    {
        $this->password = $password;
    }

    public function isActivated(): bool
    {
        return $this->activated;
    }

    public function setActivated(bool $activated): void
    {
        $this->activated = $activated;
    }

    public function getRoles(): array
    {
        return [];
    }

    public function eraseCredentials()
    {
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }
}