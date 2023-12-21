<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Enum\ImportStatus;
use App\Entity\Traits\CreatedByUserTrait;
use App\Entity\Traits\ResourceTrait;
use App\Entity\Traits\TimestampableTrait;
use App\Repository\DeckImportRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;

#[Entity(repositoryClass: DeckImportRepository::class)]
#[HasLifecycleCallbacks]
class DeckImport implements DeckImportInterface
{
    use ResourceTrait;
    use CreatedByUserTrait;
    use TimestampableTrait;

    #[Column(type: Types::STRING)]
    private ?string $filePath = null;

    #[Column(type: Types::STRING, nullable: true)]
    private ?string $errorString = null;

    #[Column(type: Types::STRING, enumType: ImportStatus::class)]
    private ImportStatus $status = ImportStatus::DRAFT;

    public function getFilePath(): ?string
    {
        return $this->filePath;
    }

    public function setFilePath(?string $filePath): void
    {
        $this->filePath = $filePath;
    }

    public function getErrorString(): ?string
    {
        return $this->errorString;
    }

    public function setErrorString(?string $errorString): void
    {
        $this->errorString = $errorString;
    }

    public function getStatus(): ImportStatus
    {
        return $this->status;
    }

    public function setStatus(ImportStatus $status): void
    {
        $this->status = $status;
    }
}