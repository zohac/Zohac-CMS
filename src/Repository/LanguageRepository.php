<?php

namespace App\Repository;

use App\Entity\Language;
use App\Interfaces\RepositoryInterface;
use App\Traits\RepositoryTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Language|null find($id, $lockMode = null, $lockVersion = null)
 * @method Language|null findOneBy(array $criteria, array $orderBy = null)
 * @method Language[]    findAll()
 * @method Language[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LanguageRepository extends ServiceEntityRepository implements RepositoryInterface
{
    use RepositoryTrait;

    const ARCHIVED = 'archived';

    private $temporaryCache = null;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Language::class);
    }

    /**
     * @param array $options
     *
     * @return Language[]
     */
    public function findAllInOneRequest(array $options = []): array
    {
        $query = $this->createQueryBuilder('l')
            ->select('l');

        return $this->executeQuery($query, 'l', $options);
    }

    /**
     * @param array $options
     *
     * @return array
     */
    public function findAllUuid(array $options = []): array
    {
        $query = $this->createQueryBuilder('l')
            ->select('l.uuid');

        return $this->executeQuery($query, 'l', $options);
    }

    /**
     * @param array $options
     *
     * @return array
     */
    public function findLanguagesForForm(array $options = []): array
    {
        if (null === $this->temporaryCache) {
            $query = $this->createQueryBuilder('l')
                ->select('l.uuid, l.iso6391');

            $this->temporaryCache = $this->executeQuery($query, 'l', $options);
        }

        return $this->temporaryCache;
    }
}
