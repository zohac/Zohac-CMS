<?php

namespace App\Dto\User;

use App\Interfaces\Dto\DtoInterface;
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
     * @Assert\Type("string")
     */
    public $locale;
}
