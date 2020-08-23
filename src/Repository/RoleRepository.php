<?php

namespace App\Repository;

use App\Entity\Role;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Role|null find($id, $lockMode = null, $lockVersion = null)
 * @method Role|null findOneBy(array $criteria, array $orderBy = null)
 * @method Role[]    findAll()
 * @method Role[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RoleRepository extends ServiceEntityRepository
{
    const ARCHIVED = 'archived';

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Role::class);
    }

    /**
     * @param array $options
     *
     * @return Role[]
     */
    public function findAllInOneRequest(array $options = [])
    {
        $query = $this->createQueryBuilder('r')
            ->select('r, t, tr, l');

        if (array_key_exists(self::ARCHIVED, $options)) {
            $archived = (bool) $options[self::ARCHIVED];

            $query = $query->andWhere('r.archived = :archived')
                ->setParameter(self::ARCHIVED, $archived);
        }

        $query = $query->leftJoin('r.translatable', 't')
            ->leftJoin('t.translations', 'tr')
            ->leftJoin('tr.language', 'l')
            ->getQuery();

        return $query->execute();
    }
}
