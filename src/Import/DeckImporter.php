<?php

declare(strict_types=1);

namespace App\Import;

use App\Entity\DeckImportInterface;
use App\Entity\DeckInterface;
use App\Entity\Enum\ImportStatus;
use App\Entity\UserInterface;
use App\Factory\CardFactoryInterface;
use App\Factory\DeckFactoryInterface;
use App\Import\DTO\BlackCardDTO;
use App\Import\DTO\DeckImportDTO;
use App\Import\DTO\WhiteCardDTO;
use App\Import\Strategy\ImportStrategyInterface;
use App\Repository\UserRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

class DeckImporter implements DeckImporterInterface
{
    private ImportStrategyInterface $importStrategy;

    public function __construct(
        private readonly DeckFactoryInterface $deckFactory,
        private readonly CardFactoryInterface $cardFactory,
        private readonly EntityManagerInterface $entityManager,
        private readonly UserRepositoryInterface $userRepository,
    ) {}

    public function setImportStrategy(ImportStrategyInterface $importStrategy): void
    {
        $this->importStrategy = $importStrategy;
    }

    public function import(DeckImportInterface $deckImport): void
    {
        $deckImport->setStatus(ImportStatus::IN_PROGRESS);
        $this->entityManager->persist($deckImport);
        $this->entityManager->flush();

        $dto = $this->importStrategy->import($deckImport->getFilePath());

        $user = $this->userRepository->find($deckImport->getCreatedBy()->getId());

        $deck = $this->deckFactory->createForUser($user);

        $deck->setName($dto->getName());

        $this->createWhiteCards($dto, $deck, $user);
        $this->createBlackCards($dto, $deck, $user);

        $this->entityManager->persist($deck);
        $this->entityManager->persist($user);

        $deckImport->setStatus(ImportStatus::IMPORTED);
        $deckImport->setDeck($deck);

        $this->entityManager->persist($deckImport);

        $this->entityManager->flush();
    }

    private function createWhiteCards(DeckImportDTO $deckImportDTO, DeckInterface $deck, UserInterface $user): void
    {
        /** @var WhiteCardDTO $whiteCard */
        foreach ($deckImportDTO->getWhiteCards() as $whiteCard) {
            $card = $this->cardFactory->createWhiteCard();

            $card->setCreatedBy($user);
            $card->setText([$whiteCard->getText()]);

            $deck->addCard($card);

            $this->entityManager->persist($card);
        }
    }

    private function createBlackCards(DeckImportDTO $deckImportDTO, DeckInterface $deck, UserInterface $user): void
    {
        /** @var BlackCardDTO $blackCard */
        foreach ($deckImportDTO->getBlackCards() as $blackCard) {
            $card = $this->cardFactory->createBlackCard();

            $card->setCreatedBy($user);
            $card->setText($blackCard->getParts());

            $deck->addCard($card);

            $this->entityManager->persist($card);
        }
    }
}