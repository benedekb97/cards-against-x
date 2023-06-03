<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\PlayInterface;
use App\Entity\TurnInterface;

interface PlayRepositoryInterface
{
    public function findOneByIdAndTurn(int $id, TurnInterface $turn): ?PlayInterface;
}