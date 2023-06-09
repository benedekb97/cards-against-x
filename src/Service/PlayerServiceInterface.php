<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

interface PlayerServiceInterface
{
    public function submitCards(Request $request): JsonResponse;
}