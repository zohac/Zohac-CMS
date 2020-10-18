<?php

namespace App\Dto\Menu;

use App\Entity\Menu;
use App\Interfaces\Dto\DtoInterface;
use App\Interfaces\EntityInterface;

/**
 * Class MenuDto.
 */
class MenuDto implements DtoInterface
{
    public $items;

    public $uuid;

    public $name;

    public $archived;

    /**
     * @param EntityInterface $entity
     *
     * @return bool
     */
    public function canHandle(EntityInterface $entity): bool
    {
        return $entity instanceof Menu;
    }
}
