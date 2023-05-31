<?php

declare(strict_types=1);

namespace App\Handler;

use App\Entity\GameInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

interface LobbyUpdateHandlerInterface
{
    public const REQUEST_KEY_NUMBER_OF_ROUNDS = 'numberOfRounds';
    public const REQUEST_KEY_DECK_ID = 'deckId';

    public function handle(Request $request, GameInterface $game): Response;
}