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

    // /**
    //  * @return Translatable[] Returns an array of Translatable objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Translatable
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
