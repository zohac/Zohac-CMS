<?php

namespace App\Traits;

use Doctrine\ORM\QueryBuilder;

trait RepositoryTrait
{
    /**
     * @param QueryBuilder $query
     * @param array        $options
     *
     * @return array
     */
    public function executeQuery(QueryBuilder $query, array $options = []): array
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