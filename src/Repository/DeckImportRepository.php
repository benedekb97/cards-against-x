<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\DeckImport;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class DeckImportRepository extends ServiceEntityRepository implements DeckImportRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DeckImport::class);
    }
}