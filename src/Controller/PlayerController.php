<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Enum\GameStatus;
use App\Entity\UserInterface;
use App\Event\GameUpdateEvent;
use App\Event\LobbyUpdateEvent;
use App\Event\TurnEvent;
use Doctrine\ORM\EntityManagerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PlayerController extends AbstractController
{
    #[Route('/ready', name: 'ready', methods: ['POST'])]
    public function ready(
        EntityManagerInterface $entityManager,
        EventDispatcherInterface $eventDispatcher
    ): Response
    {
        /** @var UserInterface $user */
        $user = $this->getUser();

        if (!$user->hasPlayer()) {
            return new JsonResponse(
                [
                    'error' => 'User not in game!',
                    'code' => Response::HTTP_BAD_REQUEST,
                ],
                Response::HTTP_BAD_REQUEST
            );
        }

        $user->getPlayer()->setReady(!$user->getPlayer()->isReady());

        $entityManager->persist($user);

        if ($user->getPlayer()->getGame()->getStatus() === GameStatus::LOBBY) {
            $eventDispatcher->dispatch(new LobbyUpdateEvent($user->getPlayer()->getGame()));
        }

        if ($user->getPlayer()->getGame()->getStatus() === GameStatus::IN_PROGRESS) {
            if ($user->getPlayer()->getGame()->allPlayersReady()) {
                $eventDispatcher->dispatch(
                    new TurnEvent($user->getPlayer()->getGame()->getCurrentRound()->getCurrentTurn())
                );
            }

            $eventDispatcher->dispatch(new GameUpdateEvent($user->getPlayer()->getGame()));
        }

        $entityManager->flush();

        return new JsonResponse(
            [
                'success' => true,
            ],
            Response::HTTP_OK
        );
    }
}