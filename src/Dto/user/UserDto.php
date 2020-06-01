<?php

namespace App\Dto\user;

use Symfony\Component\Validator\Constraints as Assert;

class UserDto
{
    /**
     * @Assert\Email()
     */
    public $email;

    public $roles;

    /**
     * @Assert\Type("string")
     */
    public $password;
}
