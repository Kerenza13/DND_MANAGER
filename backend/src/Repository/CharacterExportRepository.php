<?php

namespace App\Repository;

use App\Entity\CharacterExport;
use App\Entity\CharacterSheet;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CharacterExportRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CharacterExport::class);
    }

    public function findByCharacter(CharacterSheet $character): array
    {
        return $this->createQueryBuilder('e')
            ->leftJoin('e.character', 'c')->addSelect('c')
            ->where('e.character = :character')
            ->setParameter('character', $character)
            ->orderBy('e.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
