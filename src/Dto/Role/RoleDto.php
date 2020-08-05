<?php

namespace App\Dto\Role;

use App\Entity\Role;
use App\Interfaces\Dto\DtoInterface;
use App\Interfaces\EntityInterface;

/**
 * Class RoleDto.
 */
class RoleDto implements DtoInterface
{
    public $uuid;

    public $name;

    public $translatable;

    /**
     * @param EntityInterface $entity
     *
     * @return bool
     */
    public function canHandle(EntityInterface $entity): bool
    {
        return $entity instanceof Role;
    }
}
