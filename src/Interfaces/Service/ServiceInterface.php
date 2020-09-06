<?php

namespace App\Interfaces\Service;

use App\Interfaces\EntityInterface;

interface ServiceInterface
{
    /**
     * @param EntityInterface $entity
     *
     * @return string
     */
    public function getDeleteMessage(EntityInterface $entity): string;
}
