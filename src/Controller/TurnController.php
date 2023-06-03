<?php

declare(strict_types=1);

namespace App\Controller;

use App\Checker\UserCardSubmitActionCheckerInterface;
use App\Checker\UserWinnerSubmitActionCheckerInterface;
use App\Entity\Enum\Role;
use App\Entity\UserInterface;
use App\Service\PlayerServiceInterface;
use App\Service\TurnServiceInterface;
use PHPUnit\Util\Json;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class TurnController extends AbstractController
{
    public function __construct(
        private readonly UserCardSubmitActionCheckerInterface $userCardSubmitActionChecker,
        private readonly PlayerServiceInterface $playerService,
        private readonly UserWinnerSubmitActionCheckerInterface $userWinnerSubmitActionChecker,
        private readonly TurnServiceInterface $turnService
    ) {}

    #[Route('/submit', name: 'submit', methods: ['POST'])]
    #[IsGranted(Role::ROLE_USER->value)]
    public function submit(Request $request): Response
    {
        /** @var UserInterface $user */
        $user = $this->getUser();

        if (!$this->userCardSubmitActionChecker->check($user)) {
            return new JsonResponse(
                [
                    'error' => 'Bad request',
                    'code' => Response::HTTP_BAD_REQUEST
                ],
                Response::HTTP_BAD_REQUEST
            );
        }

        return $this->playerService->submitCards($request);
    }

    #[Route('/submit-winner', name: 'submit-winner', methods: ['POST'])]
    #[IsGranted(Role::ROLE_USER->value)]
    public function submitWinner(Request $request): Response
    {
        /** @var UserInterface $user */
        $user = $this->getUser();

        if (!$this->userWinnerSubmitActionChecker->check($user)) {
            throw new AccessDeniedHttpException();
        }

        return $this->turnService->setWinner($user, $request);
    }
}