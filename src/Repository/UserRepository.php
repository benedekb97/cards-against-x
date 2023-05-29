<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\UserInterface;
use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository implements UserRepositoryInterface
{
    public function findOneByEmail(string $email): ?UserInterface
    {
        return $this->createQueryBuilder('u')
            ->where('u.email = :email')
            ->setParameter('email', $email)
            ->getQuery()
            ->getOneOrNullResult();
    }
}