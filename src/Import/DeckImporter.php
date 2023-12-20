<?php

declare(strict_types=1);

namespace App\Import;

use App\Entity\DeckInterface;
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

    public function import(string $filePath, int $userId): void
    {
        $dto = $this->importStrategy->import($filePath);

        $user = $this->userRepository->find($userId);

        $deck = $this->deckFactory->createForUser($user);

        $deck->setName($dto->getName());
        $deck->setPublic($dto->isPublic());

        $this->createWhiteCards($dto, $deck, $user);
        $this->createBlackCards($dto, $deck, $user);

        $this->entityManager->persist($deck);
        $this->entityManager->persist($user);

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