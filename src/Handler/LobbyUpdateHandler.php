<?php

declare(strict_types=1);

namespace App\Handler;

use App\Entity\DeckInterface;
use App\Entity\GameInterface;
use App\Event\LobbyUpdateEvent;
use App\Repository\DeckRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

readonly class LobbyUpdateHandler implements LobbyUpdateHandlerInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private DeckRepositoryInterface $deckRepository,
        private EventDispatcherInterface $eventDispatcher
    ) {}

    public function handle(Request $request, GameInterface $game): Response
    {
        if (!$this->isRequestValid($request)) {
            return new JsonResponse(
                [
                    'error' => 'Bad request',
                    'code' => Response::HTTP_BAD_REQUEST,
                ],
                Response::HTTP_BAD_REQUEST
            );
        }

        $game->setNumberOfRounds($request->request->getInt(self::REQUEST_KEY_NUMBER_OF_ROUNDS));

        if (($deckId = $request->request->get(self::REQUEST_KEY_DECK_ID)) !== null) {
            /** @var DeckInterface $deck */
            $deck = $this->deckRepository->getDeckForGame($game, $deckId);

            if ($deck === null) {
                return new JsonResponse(
                    [
                        'error' => 'Bad request',
                        'code' => Response::HTTP_BAD_REQUEST,
                    ],
                    Response::HTTP_BAD_REQUEST
                );
            }

            $game->setDeck($deck);
        }

        $this->entityManager->persist($game);

        $this->eventDispatcher->dispatch(new LobbyUpdateEvent($game));

        $this->entityManager->flush();

        return new JsonResponse(
            [
                'success' => true,
            ],
            Response::HTTP_OK
        );
    }

    private function isRequestValid(Request $request): bool
    {
        $numberOfRounds = $request->request->getInt(self::REQUEST_KEY_NUMBER_OF_ROUNDS);

        if (!is_int($numberOfRounds)) {

            return false;
        }

        if ($numberOfRounds < 1) {
            return false;
        }

        if ($numberOfRounds > 10) {
            return false;
        }

        return true;
    }
}