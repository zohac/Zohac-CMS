<?php

namespace App\Dto\User;

use App\Entity\User;
use App\Interfaces\Dto\DtoInterface;
use App\Interfaces\EntityInterface;
use Symfony\Component\Validator\Constraints as Assert;

class UserDto implements DtoInterface
{
    /**
     * @Assert\Uuid()
     */
    public $uuid;

    /**
     * @Assert\Email()
     * @Assert\NotBlank()
     */
    public $email;

    public $roles = [];

    /**
     * @Assert\Type("string")
     * @ Assert\NotCompromisedPassword()
     */
    public $password;

    /**
     * @Assert\Type("string")
     */
    public $token;

    /**
     * @Assert\DateTime()
     */
    public $tokenValidity;

    /**
     * @Assert\Uuid()
     */
    public $language;

    /**
     * @param EntityInterface $entity
     *
     * @return bool
     */
    public function canHandle(EntityInterface $entity): bool
    {
        return $entity instanceof User;
    }
}
