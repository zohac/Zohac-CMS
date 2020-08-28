<?php

namespace App\Repository;

use App\Entity\Role;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use function array_key_exists;

/**
 * @method Role|null find($id, $lockMode = null, $lockVersion = null)
 * @method Role|null findOneBy(array $criteria, array $orderBy = null)
 * @method Role[]    findAll()
 * @method Role[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RoleRepository extends ServiceEntityRepository
{
    const ARCHIVED = 'archived';

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
     * @param QueryBuilder $query
     * @param array        $options
     *
     * @return array
     */
    private function executeQuery(QueryBuilder $query, array $options = []): array
    {
        if (array_key_exists(self::ARCHIVED, $options)) {
            $archived = (bool) $options[self::ARCHIVED];

            $query = $query->andWhere('r.archived = :archived')
                ->setParameter(self::ARCHIVED, $archived);
        }

        $query = $query->getQuery();

        return $query->execute();
    }

    /**
     * @param array $options
     *
     * @return Role[]
     */
    public function findAllInOneRequest(array $options = [])
    {
        $query = $this->createQueryBuilder('r')
            ->select('r, t, tr, l')
            ->leftJoin('r.translatable', 't')
            ->leftJoin('t.translations', 'tr')
            ->leftJoin('tr.language', 'l');

        return $this->executeQuery($query, $options);
    }
}
