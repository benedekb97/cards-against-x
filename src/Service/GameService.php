<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Enum\GameStatus;
use App\Entity\GameInterface;
use App\Entity\PlayerInterface;
use App\Entity\UserInterface;
use App\Event\LobbyUpdateEvent;
use App\Factory\GameFactoryInterface;
use App\Factory\PlayerFactoryInterface;
use App\Factory\RoundFactoryInterface;
use App\Factory\TurnFactoryInterface;
use App\Generator\GameSlugGeneratorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

readonly class GameService implements GameServiceInterface
{
    public function __construct(
        private GameFactoryInterface $gameFactory,
        private PlayerFactoryInterface $playerFactory,
        private EntityManagerInterface $entityManager,
        private GameSlugGeneratorInterface $gameSlugGenerator,
        private UrlGeneratorInterface $urlGenerator,
        private EventDispatcherInterface $eventDispatcher,
        private RoundFactoryInterface $roundFactory,
        private TurnFactoryInterface $turnFactory,
        private PlayerCardServiceInterface $playerCardService
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

        if ($player->isHost() && $game->getPlayers()->count() > 1) {
            /** @var PlayerInterface $newHost */
            $newHost = $game->getPlayers()->filter(
                static function (PlayerInterface $gamePlayer) use ($player) {
                    return $gamePlayer !== $player;
                }
            )->first();

            $game->setCreatedBy($newHost->getUser());
        }

        $game->removePlayer($player);

        $user->setPlayer(null);

        $this->entityManager->persist($user);
        $this->entityManager->persist($player);
        $this->entityManager->persist($game);

        $this->entityManager->flush();

        $this->eventDispatcher->dispatch(new LobbyUpdateEvent($game));

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

        $this->eventDispatcher->dispatch(new LobbyUpdateEvent($game));

        return new RedirectResponse(
            $this->urlGenerator->generate('lobby', ['slug' => $game->getSlug()])
        );
    }

    public function startGame(GameInterface $game): RedirectResponse
    {
        $firstRound = null;

        for ($roundNumber = 1; $roundNumber <= $game->getNumberOfRounds(); $roundNumber++) {
            $round = $this->roundFactory->createForGame($game);

            $round->setNumber($roundNumber);

            if ($roundNumber === 1) {
                $firstRound = $round;
            }

            $players = $game->getPlayers()->toArray();

            shuffle($players);

            $firstTurn = null;

            /** @var PlayerInterface $player */
            foreach ($players as $player) {
                $turn = $this->turnFactory->createForRound($round);

                $turn->setPlayer($player);

                $this->entityManager->persist($turn);

                $firstTurn = $turn;
            }

            $round->setCurrentTurn($firstTurn);

            $this->entityManager->persist($round);
        }

        $game->setCurrentRound($firstRound);
        $game->setStatus(GameStatus::IN_PROGRESS);

        $this->playerCardService->assignCardsToPlayers($game);

        $this->entityManager->persist($game);

        $this->eventDispatcher->dispatch(new LobbyUpdateEvent($game));

        $this->entityManager->flush();

        return new RedirectResponse(
            $this->urlGenerator->generate('game', ['slug' => $game->getSlug()])
        );
    }
}