<?php

namespace App\Dto\Role;

use App\Entity\Role;
use App\Interfaces\Dto\DtoInterface;
use App\Interfaces\EntityInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class RoleDto.
 */
class RoleDto implements DtoInterface
{
    /**
     * @Assert\Uuid()
     */
    public $uuid;

    /**
     * @Assert\Length(
     *      max = 255,
     *      maxMessage = "Your role name cannot be longer than {{ limit }} characters",
     *      allowEmptyString = true
     * )
     * @Assert\NotBlank()
     */
    public $name;

    /*
     * @Assert\All(
     *     @Assert\Collection(
     *         fields={
     *             'uuid' = @Assert\Uuid()
     *             'message' = {
     *                 @Assert\Length(
     *                     max = 255,
     *                     maxMessage = "Your role name cannot be longer than {{ limit }} characters",
     *                     allowEmptyString = true
     *                 )
     *             }
     *             'language' = @Assert\Choice(callback={"App\Service\Language\LanguageService", "getUuidLanguages"})
     *         }
     *     )
     * )
     */
    public $translatable = [];

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
