<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Entity\Enum\ImportStatus;
use App\Import\DeckImporterInterface;
use App\Import\Strategy\ImportStrategyInterface;
use App\Message\ImportDeckMessage;
use App\Repository\DeckImportRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly class ImportDeckMessageHandler
{
    public function __construct(
        private DeckImporterInterface         $deckImporter,
        private array                         $importStrategies,
        private EntityManagerInterface        $entityManager,
        private DeckImportRepositoryInterface $deckImportRepository
    ) {}

    public function __invoke(ImportDeckMessage $message): void
    {
        $deckImport = $this->deckImportRepository->find($message->getDeckImportId());

        $extension = $this->getExtension($deckImport->getFilePath());

        $this->deckImporter->setImportStrategy($this->resolveImportStrategy($extension));

        try {
            $this->deckImporter->import($deckImport);
        } catch (\Exception $exception) {
            $deckImport->setStatus(ImportStatus::ERROR);
            $deckImport->setErrorString($exception->getMessage());

            $this->entityManager->persist($deckImport);
            $this->entityManager->flush();
        }
    }

    private function getExtension(string $filePath): string
    {
        $fileParts = explode('.', $filePath);

        return end($fileParts);
    }

    private function resolveImportStrategy(string $extension): ImportStrategyInterface
    {
        /** @var ImportStrategyInterface $importStrategy */
        foreach ($this->importStrategies as $importStrategy) {
            if ($importStrategy->supports($extension)) {
                return $importStrategy;
            }
        }

        throw new \InvalidArgumentException('Could not find import strategy for extension ' . $extension);
    }
}