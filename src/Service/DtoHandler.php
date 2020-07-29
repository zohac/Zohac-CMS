<?php

namespace App\Service;

use App\Exception\DtoHandlerException;
use App\Interfaces\Dto\DtoInterface;
use App\Interfaces\EntityInterface;

class DtoHandler
{
    /**
     * @var DtoInterface[]
     */
    private $dtoInterfaces = [];

    /**
     * DtoFactory constructor.
     *
     * @param iterable $handlers
     */
    public function __construct(iterable $handlers)
    {
        foreach ($handlers as $handler) {
            $this->dtoInterfaces[] = $handler;
        }
    }

    /**
     * @param EntityInterface $entity
     *
     * @return DtoInterface
     *
     * @throws DtoHandlerException
     */
    public function getDtoInterface(EntityInterface $entity): DtoInterface
    {
        foreach ($this->dtoInterfaces as $dtoInterface) {
            if ($dtoInterface->canHandle($entity)) {
                return $dtoInterface;
            }
        }

        throw new DtoHandlerException('No Dto interface for this entity');
    }
}
