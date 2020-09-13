<?php

namespace App\Repository;

use App\Entity\Maintenance;
use App\Interfaces\RepositoryInterface;
use App\Traits\RepositoryTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Maintenance|null find($id, $lockMode = null, $lockVersion = null)
 * @method Maintenance|null findOneBy(array $criteria, array $orderBy = null)
 * @method Maintenance[]    findAll()
 * @method Maintenance[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MaintenanceRepository extends ServiceEntityRepository implements RepositoryInterface
{
    use RepositoryTrait;

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
    public function findAllInOneRequest(array $options = []): array
    {
        $query = $this->createQueryBuilder('m')
            ->select('m');

        return $this->executeQuery($query, 'm', $options);
    }

    /**
     * @return Maintenance|null
     */
    public function getMaintenance(): ?Maintenance
    {
        $maintenances = $this->findAll();

        if (empty($maintenances)) {
            return null;
        }

        return $maintenances[0];
    }
}
