<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Play;
use App\Entity\PlayInterface;
use App\Entity\TurnInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class PlayRepository extends ServiceEntityRepository implements PlayRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Play::class);
    }

    public function findOneByIdAndTurn(int $id, TurnInterface $turn): ?PlayInterface
    {
        return $this->createQueryBuilder('p')
            ->where('p.id = :id')
            ->andWhere('p.turn = :turn')
            ->setParameter('id', $id)
            ->setParameter('turn', $turn)
            ->getQuery()
            ->getOneOrNullResult();
    }
}