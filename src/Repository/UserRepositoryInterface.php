<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\UserInterface;
use Doctrine\Persistence\ObjectRepository;

interface UserRepositoryInterface extends ObjectRepository
{
    public function findOneByEmail(string $email): ?UserInterface;
}