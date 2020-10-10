<?php

namespace App\Repository;

use App\Entity\HTMLAnchorElement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method HTMLAnchorElement|null find($id, $lockMode = null, $lockVersion = null)
 * @method HTMLAnchorElement|null findOneBy(array $criteria, array $orderBy = null)
 * @method HTMLAnchorElement[]    findAll()
 * @method HTMLAnchorElement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HTMLAnchorElementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, HTMLAnchorElement::class);
    }

    // /**
    //  * @return HTMLAnchorElement[] Returns an array of HTMLAnchorElement objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('h.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?HTMLAnchorElement
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
