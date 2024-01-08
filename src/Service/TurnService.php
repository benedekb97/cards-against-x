<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\PlayInterface;
use App\Entity\UserInterface;
use App\Message\EndTurnMessage;
use App\Processor\Game\GameProcessor;
use App\Repository\PlayRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DelayStamp;

readonly class TurnService implements TurnServiceInterface
{
    public function __construct(
        private PlayRepositoryInterface $playRepository,
        private EntityManagerInterface $entityManager,
        private MessageBusInterface $messageBus,
        private GameProcessor $gameProcessor,
    ) {}

    public function setWinner(UserInterface $user, Request $request): JsonResponse
    {
        if (($play = $this->validateRequest($user, $request)) === false) {
            return new JsonResponse(
                [
                    'error' => 'Bad request',
                    'code' => Response::HTTP_BAD_REQUEST,
                ],
                Response::HTTP_BAD_REQUEST
            );
        }

        $turn = $user->getPlayer()->getGame()->getCurrentRound()->getCurrentTurn();

        $turn->setWinningPlay($play);

        $play->setPoints($user->getPlayer()->getGame()->getPlayers()->count());

        $this->entityManager->persist($turn);
        $this->gameProcessor->process($user->getPlayer()->getGame());

        $this->messageBus->dispatch(
            new EndTurnMessage($turn->getId()),
            [
                // wait 60 seconds before ending the turn
                new DelayStamp(10 * 1000),
            ]
        );

        return new JsonResponse(
            [
                'success' => true,
            ]
        );
    }

    private function validateRequest(UserInterface $user, Request $request): bool|PlayInterface
    {
        if (!$request->request->has('play')) {
            return false;
        }

        return $this->playRepository->findOneByIdAndTurn(
            $request->request->getInt('play'),
            $user->getPlayer()->getGame()->getCurrentRound()->getCurrentTurn()
        ) ?? false;
    }
}