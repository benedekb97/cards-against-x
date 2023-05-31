<?php

declare(strict_types=1);

namespace App\Controller;

use App\Checker\UserHostEligibilityCheckerInterface;
use App\Checker\UserJoinActionCheckerInterface;
use App\Checker\UserLeaveActionCheckerInterface;
use App\Checker\UserUpdateLobbyActionCheckerInterface;
use App\Entity\Enum\Role;
use App\Entity\UserInterface;
use App\Handler\LobbyUpdateHandlerInterface;
use App\Handler\UserHostIneligibilityHandler;
use App\Repository\DeckRepositoryInterface;
use App\Repository\GameRepositoryInterface;
use App\Resolver\CurrentGameRedirectResolverInterface;
use App\Service\GameServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class GameController extends AbstractController
{
    public function __construct(
        private readonly UserHostEligibilityCheckerInterface $userHostEligibilityChecker,
        private readonly UserHostIneligibilityHandler $userHostIneligibilityHandler,
        private readonly GameServiceInterface $gameService,
        private readonly GameRepositoryInterface $gameRepository,
        private readonly UserLeaveActionCheckerInterface $userLeaveActionChecker,
        private readonly CurrentGameRedirectResolverInterface $currentGameRedirectResolver,
        private readonly UserJoinActionCheckerInterface $userJoinActionChecker,
        private readonly DeckRepositoryInterface $deckRepository,
        private readonly UserUpdateLobbyActionCheckerInterface $updateLobbyActionChecker,
        private readonly LobbyUpdateHandlerInterface $lobbyUpdateHandler
    ) {}

    #[Route('/create', name: 'create')]
    #[IsGranted(Role::ROLE_USER->value)]
    public function create(): RedirectResponse
    {
        /** @var UserInterface $user */
        $user = $this->getUser();

        if (!$this->userHostEligibilityChecker->check($user)) {
            return $this->userHostIneligibilityHandler->handle($user);
        }

        return $this->gameService->createGame($user);
    }

    #[Route('lobby/{slug}', name: 'lobby')]
    #[IsGranted(Role::ROLE_USER->value)]
    public function lobby(string $slug): Response
    {
        $game = $this->gameRepository->findOneBySlug($slug, false);

        if ($game === null) {
            throw new NotFoundHttpException();
        }

        if (!$game->getPlayers()->contains($this->getUser()->getPlayer())) {
            throw new NotFoundHttpException();
        }

        return $this->render(
            'game/lobby.html.twig',
            [
                'game' => $game,
                'decks' => $this->deckRepository->getDecksForGame($game),
                'isCreator' => $this->getUser() === $game->getCreatedBy(),
                'player' => $this->getUser()->getPlayer(),
            ]
        );
    }

    #[Route('update-lobby/{slug}', name: 'update-lobby', methods: ['POST'])]
    #[IsGranted(Role::ROLE_USER->value)]
    public function updateLobby(string $slug, Request $request): Response
    {
        $game = $this->gameRepository->findOneBySlug($slug);

        if ($game === null) {
            throw new NotFoundHttpException();
        }

        /** @var UserInterface $user */
        $user = $this->getUser();

        if (!$this->updateLobbyActionChecker->check($user, $game)) {
            return new JsonResponse(
                [
                    'error' => 'Forbidden',
                    'code' => Response::HTTP_FORBIDDEN,
                ],
                Response::HTTP_FORBIDDEN
            );
        }

        return $this->lobbyUpdateHandler->handle($request, $game);
    }

    #[Route('/leave', name: 'leave')]
    #[IsGranted(Role::ROLE_USER->value)]
    public function leave(): RedirectResponse
    {
        /** @var UserInterface $user */
        $user = $this->getUser();

        if (!$this->userLeaveActionChecker->check($user)) {
            return $this->currentGameRedirectResolver->resolve($user);
        }

        return $this->gameService->leaveGame($user);
    }

    #[Route('/join/{slug}', name: 'join')]
    #[IsGranted(Role::ROLE_USER->value)]
    public function join(string $slug): RedirectResponse
    {
        /** @var UserInterface $user */
        $user = $this->getUser();

        $game = $this->gameRepository->findOneBySlug($slug, false);

        if (null === $game) {
            throw new NotFoundHttpException();
        }

        if (!$this->userJoinActionChecker->check($user, $game)) {
            return $this->currentGameRedirectResolver->resolve($user);
        }

        return $this->gameService->joinGame($user, $game);
    }
}