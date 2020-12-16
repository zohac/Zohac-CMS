<?php

namespace App\Traits;

use Doctrine\ORM\QueryBuilder;

trait RepositoryTrait
{
    /**
     * @param QueryBuilder $query
     * @param string       $entityType
     * @param array        $options
     *
     * @return array
     */
    public function executeQuery(QueryBuilder $query, string $entityType, array $options = []): array
    {
        if (\array_key_exists(self::ARCHIVED, $options)) {
            $archived = (bool) $options[self::ARCHIVED];

            $query = $query->andWhere($entityType.'.archived = :archived')
                ->setParameter(self::ARCHIVED, $archived);
        }

        $query = $query->getQuery();

        return $query->execute();
    }
}
