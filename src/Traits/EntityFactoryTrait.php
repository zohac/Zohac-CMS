<?php

namespace App\Traits;

use App\Exception\UuidException;
use App\Service\UuidService;
use ReflectionClass;
use ReflectionException;

trait EntityFactoryTrait
{
    /**
     * @var UuidService
     */
    private $uuidService;

    /**
     * EntityFactoryTrait constructor.
     *
     * @param UuidService $uuidService
     */
    public function __construct(UuidService $uuidService)
    {
        $this->uuidService = $uuidService;
    }

    /**
     * @param object|string $object
     *
     * @return ReflectionClass
     *
     * @throws ReflectionException
     */
    public function getNewReflectionClass($object): ReflectionClass
    {
        return new ReflectionClass($object);
    }

    /**
     * @return string
     *
     * @throws UuidException
     */
    public function getUuid(): string
    {
        return $this->uuidService->create();
    }
}
