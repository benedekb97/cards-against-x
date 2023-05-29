<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Traits\CreatedByUserTrait;
use App\Entity\Traits\DeletableTrait;
use App\Entity\Traits\HasGameTrait;
use App\Entity\Traits\ResourceTrait;
use App\Entity\Traits\TimestampableTrait;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\ORM\Mapping\ManyToOne;

#[Entity]
#[HasLifecycleCallbacks]
class Message implements MessageInterface
{
    use ResourceTrait;
    use CreatedByUserTrait;
    use DeletableTrait;
    use TimestampableTrait;
    use HasGameTrait;

    #[Column(type: Types::STRING)]
    private ?string $message = null;

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): void
    {
        $this->message = $message;
    }
}