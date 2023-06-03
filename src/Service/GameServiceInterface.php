<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\GameInterface;
use App\Entity\UserInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

interface GameServiceInterface
{
    public function createGame(UserInterface $user): RedirectResponse;

    public function leaveGame(UserInterface $user): RedirectResponse;

    public function joinGame(UserInterface $user, GameInterface $game): RedirectResponse;

    public function startGame(GameInterface $game): RedirectResponse;

    public function setPlayersNotReady(GameInterface $game): void;
}