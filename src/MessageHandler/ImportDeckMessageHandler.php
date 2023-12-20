<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Import\DeckImporterInterface;
use App\Import\Strategy\ImportStrategyInterface;
use App\Message\ImportDeckMessage;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class ImportDeckMessageHandler
{
    public function __construct(
        private readonly DeckImporterInterface $deckImporter,
        private readonly array $importStrategies
    ) {}

    public function __invoke(ImportDeckMessage $message): void
    {
        $extension = $this->getExtension($message->getFileLocation());

        $this->deckImporter->setImportStrategy($this->resolveImportStrategy($extension));

        $this->deckImporter->import($message->getFileLocation(), $message->getUserId());
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