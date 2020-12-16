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
}
