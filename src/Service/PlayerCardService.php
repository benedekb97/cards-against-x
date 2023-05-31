<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\CardInterface;
use App\Entity\GameInterface;
use App\Entity\PlayerInterface;
use Doctrine\Common\Collections\Collection;

readonly class PlayerCardService implements PlayerCardServiceInterface
{
    public function __construct(
        private GameCardServiceInterface $gameCardService
    ) {}

    public function assignCardsToPlayers(GameInterface $game): void
    {
        $availableCards = $this->getAvailableWhiteCards($game);

        /** @var PlayerInterface $player */
        foreach ($game->getPlayers() as $player) {

            $playerCardCount = $player->getCards()->count();

            for ($i = 0; $i < (10 - $playerCardCount); $i++) {
                $player->addCard(
                    $card = $availableCards->get(array_rand($availableCards->toArray()))
                );

                $availableCards->removeElement($card);
            }
        }
    }

    private function getAvailableWhiteCards(GameInterface $game): Collection
    {
        $deckCards = $game->getDeck()->getWhiteCards();

        $dealtCards = $this->gameCardService->getDealtWhiteCards($game);

        /** @var CardInterface $dealtCard */
        foreach ($dealtCards as $dealtCard) {
            $deckCards->removeElement($dealtCard);
        }

        $playedCards = $this->gameCardService->getPlayedWhiteCards($game);

        if ($deckCards->count() - $playedCards->count() > $this->calculateNecessaryWhiteCards($game)) {
            /** @var CardInterface $playedCard */
            foreach ($playedCards as $playedCard) {
                $deckCards->removeElement($playedCard);
            }
        }

        return $deckCards;
    }

    private function calculateNecessaryWhiteCards(GameInterface $game): int
    {
        $sum = 0;

        /** @var PlayerInterface $player */
        foreach ($game->getPlayers() as $player) {
            $sum += 10 - $player->getCards()->count();
        }

        return $sum;
    }
}