<?php

namespace App\Repository;

use App\Entity\Translatable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Translatable|null find($id, $lockMode = null, $lockVersion = null)
 * @method Translatable|null findOneBy(array $criteria, array $orderBy = null)
 * @method Translatable[]    findAll()
 * @method Translatable[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TranslatableRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Translatable::class);
    }
}
