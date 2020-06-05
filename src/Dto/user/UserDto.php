<?php

namespace App\Dto\user;

use Symfony\Component\Validator\Constraints as Assert;

class UserDto
{
    /**
     * @Assert\Uuid()
     */
    public $uuid;

    /**
     * @Assert\Email()
     */
    public $email;

    public $roles = [];

    /**
     * @Assert\Type("string")
     */
    public $password;
}
