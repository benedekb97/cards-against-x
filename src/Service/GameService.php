<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Enum\GameStatus;
use App\Entity\GameInterface;
use App\Entity\UserInterface;
use App\Factory\GameFactoryInterface;
use App\Factory\PlayerFactoryInterface;
use App\Generator\GameSlugGeneratorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

readonly class GameService implements GameServiceInterface
{
    public function __construct(
        private GameFactoryInterface $gameFactory,
        private PlayerFactoryInterface $playerFactory,
        private EntityManagerInterface $entityManager,
        private GameSlugGeneratorInterface $gameSlugGenerator,
        private UrlGeneratorInterface $urlGenerator
    ) {}

    public function createGame(UserInterface $user): RedirectResponse
    {
        $player = $this->playerFactory->createForUser($user);

        $game = $this->gameFactory->createForPlayer($player);

        $game->setSlug(
            $this->gameSlugGenerator->generate()
        );

        $this->entityManager->persist($player);
        $this->entityManager->persist($user);
        $this->entityManager->persist($game);

        $this->entityManager->flush();

        return new RedirectResponse(
            $this->urlGenerator->generate('lobby', ['slug' => $game->getSlug()])
        );
    }

    public function leaveGame(UserInterface $user): RedirectResponse
    {
        $player = $user->getPlayer();
        $game = $player->getGame();

        if ($game->getPlayers()->count() === 1) {
            $game->setStatus(GameStatus::CANCELED);

            $game->delete();
        }

        $user->getPlayer()->delete();

        $user->setPlayer(null);

        $this->entityManager->persist($user);
        $this->entityManager->persist($player);
        $this->entityManager->persist($game);

        $this->entityManager->flush();

        return new RedirectResponse(
            $this->urlGenerator->generate('index')
        );
    }

    public function joinGame(UserInterface $user, GameInterface $game): RedirectResponse
    {
        $player = $this->playerFactory->createForUser($user);

        $game->addPlayer($player);

        $this->entityManager->persist($player);
        $this->entityManager->persist($user);

        $this->entityManager->flush();

        return new RedirectResponse(
            $this->urlGenerator->generate('lobby', ['slug' => $game->getSlug()])
        );
    }
}