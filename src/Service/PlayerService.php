<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\CardInterface;
use App\Entity\TurnInterface;
use App\Entity\UserInterface;
use App\Event\GameUpdateEvent;
use App\Event\TurnEvent;
use App\Factory\PlayFactoryInterface;
use App\Repository\CardRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

readonly class PlayerService implements PlayerServiceInterface
{
    public function __construct(
        private Security $security,
        private CardRepositoryInterface $cardRepository,
        private PlayFactoryInterface $playFactory,
        private EntityManagerInterface $entityManager,
        private EventDispatcherInterface $eventDispatcher
    ) {}

    public function submitCards(Request $request): JsonResponse
    {
        /** @var UserInterface $user */
        $user = $this->security->getUser();

        $turn = $user->getPlayer()->getGame()->getCurrentRound()->getCurrentTurn();

        if (($cards = $this->validateSubmitRequest($request, $turn, $user)) === false) {
            return new JsonResponse(
                [
                    'error' => 'Bad request',
                    'code' => Response::HTTP_BAD_REQUEST,
                ],
                Response::HTTP_BAD_REQUEST
            );
        }

        $play = $this->playFactory->createForPlayerAndTurn($user->getPlayer(), $turn);

        /** @var CardInterface $card */
        foreach ($cards as $card) {
            $play->addCard($card);

            $user->getPlayer()->removeCard($card);
        }

        $this->entityManager->persist($play);
        $this->entityManager->persist($user->getPlayer());

        $this->eventDispatcher->dispatch(new TurnEvent($turn));
        $this->eventDispatcher->dispatch(new GameUpdateEvent($turn->getRound()->getGame()));

        $this->entityManager->persist($turn);
        $this->entityManager->persist($turn->getRound()->getGame());
        $this->entityManager->flush();

        return new JsonResponse(
            [
                'success' => true,
            ]
        );
    }

    private function validateSubmitRequest(Request $request, TurnInterface $turn, UserInterface $user): false|array
    {
        $cardsSubmitted = $request->request->all('ids');

        if ($cardsSubmitted !== array_filter($cardsSubmitted, 'is_int')) {
            return false;
        }

        $cards = [];

        foreach ($cardsSubmitted as $cardId) {
            /** @var CardInterface $card */
            $card = $this->cardRepository->find($cardId);

            if ($card === null || !$card->hasDeck($turn->getRound()->getGame()->getDeck())) {
                return false;
            }

            if (!$user->getPlayer()->getCards()->contains($card)) {
                return false;
            }

            $cards[] = $card;
        }

        return $cards;
    }
}