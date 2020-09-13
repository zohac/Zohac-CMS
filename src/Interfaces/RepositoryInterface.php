<?php

namespace App\Interfaces;

use Doctrine\ORM\QueryBuilder;

interface RepositoryInterface
{
    /**
     * @param array $options
     *
     * @return array
     */
    public function findAllInOneRequest(array $options = []): array;

    /**
     * @param QueryBuilder $query
     * @param string $entityType
     * @param array $options
     * @return array
     */
    public function executeQuery(QueryBuilder $query, string $entityType, array $options = []): array;
}
