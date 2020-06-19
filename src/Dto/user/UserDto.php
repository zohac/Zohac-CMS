<?php

namespace App\Dto\User;

use Symfony\Component\Validator\Constraints as Assert;

class UserDto
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
}
