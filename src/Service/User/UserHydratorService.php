<?php

namespace App\Service\User;

use App\Dto\User\UserDto;
use App\Entity\User;
use App\Exception\UuidException;
use App\Interfaces\Dto\DtoInterface;
use App\Interfaces\EntityInterface;
use App\Interfaces\Service\EntityHydratorInterface;
use App\Service\UuidService;
use ReflectionClass;
use ReflectionException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserHydratorService implements EntityHydratorInterface
{
    /**
     * @var UuidService
     */
    private $uuidService;
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    /**
     * UserHydratorService constructor.
     *
     * @param UuidService                  $uuidService
     * @param UserPasswordEncoderInterface $passwordEncoder
     */
    public function __construct(UuidService $uuidService, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->uuidService = $uuidService;
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * {@inheritdoc}
     *
     * @throws UuidException
     */
    public function getUuid(): string
    {
        return $this->uuidService->create();
    }

    /**
     * {@inheritdoc}
     *
     * @throws ReflectionException
     */
    public function getNewReflectionClass($object): ReflectionClass
    {
        return new ReflectionClass($object);
    }

    /**
     * {@inheritdoc}
     *
     * @var User
     * @var UserDto $dto
     *
     * @throws ReflectionException
     * @throws UuidException
     */
    public function hydrateEntityWithDto(EntityInterface $entity, DtoInterface $dto): EntityInterface
    {
        $reflectionDto = $this->getNewReflectionClass($dto);
        $reflectionEntity = $this->getNewReflectionClass($entity);

        foreach ($reflectionDto->getProperties() as $property) {
            $propertyName = $property->getName();
            $setMethod = 'set'.ucfirst($propertyName);

            if ($reflectionEntity->hasMethod($setMethod)) {
                $this
                    ->uuidProperty($propertyName, $dto)
                    ->notNullProperty($propertyName, $dto, $setMethod, $entity);
            }
        }

        $password = $this->passwordEncoder->encodePassword($entity, $entity->getPassword());
        $entity->setPassword($password);

        return $entity;
    }

    /**
     * {@inheritdoc}
     */
    public function hydrateDtoWithEntity(EntityInterface $entity, DtoInterface $dto): DtoInterface
    {
        // TODO: Implement hydrateDtoWithEntity() method.

        return $dto;
    }

    /**
     * @param string       $propertyName
     * @param DtoInterface $dto
     *
     * @return $this
     *
     * @throws UuidException
     */
    private function uuidProperty(string $propertyName, DtoInterface $dto): self
    {
        if ('uuid' === $propertyName && null === $dto->$propertyName) {
            $dto->$propertyName = $this->getUuid();
        }

        return $this;
    }

    /**
     * @param string          $propertyName
     * @param DtoInterface    $dto
     * @param string          $setMethod
     * @param EntityInterface $entity
     *
     * @return $this
     */
    private function notNullProperty(
        string $propertyName,
        DtoInterface $dto,
        string $setMethod,
        EntityInterface $entity
    ): self {
        if (null !== $dto->$propertyName) {
            $entity->$setMethod($dto->$propertyName);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function canHandle(EntityInterface $entity): bool
    {
        return $entity instanceof User;
    }
}
