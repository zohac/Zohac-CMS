<?php

namespace App\Interfaces;

use Doctrine\ORM\QueryBuilder;

interface RepositoryInterface
{
    const ARCHIVED = 'archived';

    /**
     * @param array $options
     * @return array
     */
    public function findAllInOneRequest(array $options = []): array;

    /**
     * @param QueryBuilder $query
     * @param array $options
     * @return array
     */
    public function executeQuery(QueryBuilder $query, array $options = []): array;
}