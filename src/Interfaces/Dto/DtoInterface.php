<?php

namespace App\Interfaces\Dto;

use App\Interfaces\EntityInterface;

interface DtoInterface
{
    /**
     * @param EntityInterface $entityName
     *
     * @return bool
     */
    public function canHandle(EntityInterface $entityName): bool;
}
