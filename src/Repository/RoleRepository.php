<?php

namespace App\Repository;

use App\Entity\Role;
use App\Interfaces\RepositoryInterface;
use App\Traits\RepositoryTrait;
use function array_key_exists;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Role|null find($id, $lockMode = null, $lockVersion = null)
 * @method Role|null findOneBy(array $criteria, array $orderBy = null)
 * @method Role[]    findAll()
 * @method Role[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RoleRepository extends ServiceEntityRepository implements RepositoryInterface
{
    use RepositoryTrait;

    private $temporaryCache = null;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Role::class);
    }

    /**
     * @param string $uuid
     *
     * @return Role|null
     *
     * @throws NonUniqueResultException
     */
    public function findOneByUuid(string $uuid): ?Role
    {
        $query = $this->createQueryBuilder('r')
            ->select('r, t, tr, l')
            ->leftJoin('r.translatable', 't')
            ->leftJoin('t.translations', 'tr')
            ->leftJoin('tr.language', 'l')
            ->andWhere('r.uuid = :uuid')
            ->setParameter('uuid', $uuid)
            ->getQuery();

        return $query->getOneOrNullResult();
    }

    /**
     * @param array $options
     *
     * @return array
     */
    public function findRolesForForm(array $options = []): array
    {
        if (null === $this->temporaryCache) {
            $query = $this->createQueryBuilder('r')
                ->select('r.uuid, r.name');

            $this->temporaryCache = $this->executeQuery($query, $options);
        }

        return $this->temporaryCache;
    }

    /**
     * @param array $options
     *
     * @return Role[]
     */
    public function findAllInOneRequest(array $options = []): array
    {
        $query = $this->createQueryBuilder('r')
            ->select('r, t, tr, l')
            ->leftJoin('r.translatable', 't')
            ->leftJoin('t.translations', 'tr')
            ->leftJoin('tr.language', 'l');

        return $this->executeQuery($query, $options);
    }
}
