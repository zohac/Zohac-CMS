<?php

namespace App\Service;

use App\Exception\HydratorException;
use App\Interfaces\Dto\DtoInterface;
use App\Interfaces\EntityInterface;
use App\Interfaces\Service\EntityHydratorInterface;
use App\Interfaces\Service\HydratorInterface;

class HydratorService implements HydratorInterface
{
    /**
     * @var EntityHydratorInterface[]
     */
    private $hydrators;

    /**
     * HydratorService constructor.
     *
     * @param iterable $handlers
     */
    public function __construct(iterable $handlers)
    {
        foreach ($handlers as $handler) {
            $this->hydrators[] = $handler;
        }
    }

    /**
     * {@inheritdoc}
     *
     * @throws HydratorException
     */
    public function hydrateEntityWithDto(EntityInterface $entity, DtoInterface $dto): EntityInterface
    {
        $hydrator = $this->handleHydrator($entity);

        return $hydrator->hydrateEntityWithDto($entity, $dto);
    }

    /**
     * {@inheritdoc}
     *
     * @throws HydratorException
     */
    public function handleHydrator(EntityInterface $entity): EntityHydratorInterface
    {
        foreach ($this->hydrators as $hydrator) {
            if ($hydrator->canHandle($entity)) {
                return $hydrator;
            }
        }

        throw new HydratorException('No hydrator for this entity.');
    }

    /**
     * {@inheritdoc}
     *
     * @throws HydratorException
     */
    public function hydrateDtoWithEntity(EntityInterface $entity, DtoInterface $dto): DtoInterface
    {
        $hydrator = $this->handleHydrator($entity);

        return $hydrator->hydrateDtoWithEntity($entity, $dto);
    }
}
