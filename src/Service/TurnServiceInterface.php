<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\UserInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

interface TurnServiceInterface
{
    public function setWinner(UserInterface $user, Request $request): JsonResponse;
}