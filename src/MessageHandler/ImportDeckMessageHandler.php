<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Entity\DeckImportInterface;
use App\Entity\Enum\ImportStatus;
use App\Import\DeckImporterInterface;
use App\Import\Strategy\ImportStrategyInterface;
use App\Message\ImportCompletedMessage;
use App\Message\ImportDeckMessage;
use App\Repository\DeckImportRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\SerializerInterface;

#[AsMessageHandler]
readonly class ImportDeckMessageHandler
{
    public function __construct(
        private DeckImporterInterface         $deckImporter,
        private array                         $importStrategies,
        private EntityManagerInterface        $entityManager,
        private DeckImportRepositoryInterface $deckImportRepository,
        private HubInterface                  $hub,
        private UrlGeneratorInterface         $urlGenerator,
        private ParameterBagInterface         $parameterBag,
        private SerializerInterface           $serializer
    ) {}

    public function __invoke(ImportDeckMessage $message): void
    {
        /** @var DeckImportInterface $deckImport */
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

        $host = trim($this->parameterBag->get('app.host'), DIRECTORY_SEPARATOR);

        $importCompletedMessage = new ImportCompletedMessage(
            $url = $this->urlGenerator->generate('decks.import.view', ['importId' => $deckImport->getId()]),
            $deckImport->getDeck()->getId()
        );

        $this->hub->publish(
            new Update(
                $host . $url,
                $this->serializer->serialize($importCompletedMessage, 'json')
            )
        );
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