<?php

namespace App\Repository;

use App\Entity\Menu;
use App\Interfaces\RepositoryInterface;
use App\Traits\RepositoryTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Menu|null find($id, $lockMode = null, $lockVersion = null)
 * @method Menu|null findOneBy(array $criteria, array $orderBy = null)
 * @method Menu[]    findAll()
 * @method Menu[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MenuRepository extends ServiceEntityRepository implements RepositoryInterface
{
    use RepositoryTrait;

    const ARCHIVED = 'archived';

    /**
     * MenuRepository constructor.
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Menu::class);
    }

    /**
     * @param array $options
     *
     * @return Menu[]
     */
    public function findAllInOneRequest(array $options = []): array
    {
        $query = $this->createQueryBuilder('m')
            ->select('m');

        return $this->executeQuery($query, 'm', $options);
    }
}
