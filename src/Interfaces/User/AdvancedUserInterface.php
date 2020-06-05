<?php

namespace App\Interfaces\User;

use Symfony\Component\Security\Core\User\UserInterface;

interface AdvancedUserInterface extends UserInterface
{
    /**
     * @return string
     */
    public function getUuid();

    /**
     * Returns the token of the user.
     *
     * @return string|null The user token
     */
    public function getToken();

    /**
     * Return the token validity.
     *
     * @return \DateTimeInterface|null
     */
    public function getTokenValidity();

    /**
     * @return bool
     */
    public function isArchived();
}
