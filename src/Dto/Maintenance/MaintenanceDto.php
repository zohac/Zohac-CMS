<?php

namespace App\Dto\Maintenance;

use App\Entity\Maintenance;
use App\Interfaces\Dto\DtoInterface;
use App\Interfaces\EntityInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class MaintenanceDto.
 */
class MaintenanceDto implements DtoInterface
{
    public $redirectPath;

    public $mode;

    public $ips;

    public $uuid;

    public $archived;

    /**
     * @param EntityInterface $entity
     *
     * @return bool
     */
    public function canHandle(EntityInterface $entity): bool
    {
        return $entity instanceof Maintenance;
    }
}
