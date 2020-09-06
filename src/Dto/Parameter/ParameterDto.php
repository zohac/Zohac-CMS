<?php

namespace App\Dto\Parameter;

use App\Entity\Parameter;
use App\Interfaces\Dto\DtoInterface;
use App\Interfaces\EntityInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class ParameterDto.
 */
class ParameterDto implements DtoInterface
{
    public $name;

    public $value;

    /**
     * @param EntityInterface $entity
     *
     * @return bool
     */
    public function canHandle(EntityInterface $entity): bool
    {
        return $entity instanceof Parameter;
    }
}