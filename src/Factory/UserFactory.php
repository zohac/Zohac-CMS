<?php

namespace App\Factory;

use App\Entity\User;
use App\Interfaces\Factory\EntityFactoryInterface;

class UserFactory implements EntityFactoryInterface
{
    public const RELATED_ENTITY = User::class;

    /**
     * @return string
     */
    public function getRelatedEntity(): string
    {
        return self::RELATED_ENTITY;
    }
}
