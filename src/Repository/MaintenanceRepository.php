<?php

namespace App\Repository;

use App\Entity\Maintenance;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Maintenance|null find($id, $lockMode = null, $lockVersion = null)
 * @method Maintenance|null findOneBy(array $criteria, array $orderBy = null)
 * @method Maintenance[]    findAll()
 * @method Maintenance[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MaintenanceRepository extends ServiceEntityRepository
{
    const ARCHIVED = 'archived';

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Maintenance::class);
    }

    /**
     * @param array $options
     *
     * @return Maintenance[]
     */
    public function findAllInOneRequest(array $options = [])
    {
        $query = $this->createQueryBuilder('m')
            ->select('m');

        return $this->executeQuery($query, $options);
    }

    /**
     * @param QueryBuilder $query
     * @param array        $options
     *
     * @return array
     */
    private function executeQuery(QueryBuilder $query, array $options = []): array
    {
        if (\array_key_exists(self::ARCHIVED, $options)) {
            $archived = (bool) $options[self::ARCHIVED];

            $query = $query->andWhere('l.archived = :archived')
                ->setParameter(self::ARCHIVED, $archived);
        }

        $query = $query->getQuery();

        return $query->execute();
    }
}
