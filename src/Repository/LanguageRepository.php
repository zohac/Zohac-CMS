<?php

namespace App\Repository;

use App\Entity\Language;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use function _HumbugBox58fd4d9e2a25\Sodium\crypto_aead_chacha20poly1305_ietf_decrypt;

/**
 * @method Language|null find($id, $lockMode = null, $lockVersion = null)
 * @method Language|null findOneBy(array $criteria, array $orderBy = null)
 * @method Language[]    findAll()
 * @method Language[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LanguageRepository extends ServiceEntityRepository
{
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
    public function findAllInOneRequest(array $options = [])
    {
        $query = $this->createQueryBuilder('l')
            ->select('l');

        return $this->executeQuery($query, $options);
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

        return $this->executeQuery($query, $options);
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
        if (\array_key_exists(self::ARCHIVED, $options)) {
            $archived = (bool) $options[self::ARCHIVED];

            $query = $query->andWhere('l.archived = :archived')
                ->setParameter(self::ARCHIVED, $archived);
        }

        $query = $query->getQuery();

        return $query->execute();
    }
}
