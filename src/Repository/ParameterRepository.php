<?php

namespace App\Repository;

use App\Entity\Parameter;
use App\Interfaces\RepositoryInterface;
use App\Traits\RepositoryTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Parameter|null find($id, $lockMode = null, $lockVersion = null)
 * @method Parameter|null findOneBy(array $criteria, array $orderBy = null)
 * @method Parameter[]    findAll()
 * @method Parameter[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ParameterRepository extends ServiceEntityRepository implements RepositoryInterface
{
    use RepositoryTrait;

    /**
     * ParameterRepository constructor.
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Parameter::class);
    }

    /**
     * @param array $options
     *
     * @return Parameter[]
     */
    public function findAllInOneRequest(array $options = []): array
    {
        $query = $this->createQueryBuilder('p')
            ->select('p');

        return $this->executeQuery($query, $options);
    }
}
