<?php

namespace App\Service;

use App\Exception\UuidException;

class UuidService
{
    /**
     * @return string
     *
     * @throws UuidException
     */
    public function create()
    {
        if ($this->functionExist('uuid_create')) {
            $counter = 0;
            do {
                $uuid = uuid_create(UUID_TYPE_RANDOM);
                ++$counter;
            } while (!$this->isValid($uuid) && 5 > $i);

            if (!$this->isValid($uuid)) {
                throw new UuidException('L\'application ne parvient pas à générer un uuid v4 valide.');
            }

            return $uuid;
        }

        throw new UuidException('La fonction uuid_create de php n\'existe pas.');
    }

    /**
     * @param string $functionName
     *
     * @return bool
     */
    public function functionExist(string $functionName): bool
    {
        return function_exists($functionName);
    }

    /**
     * @param string $uuid
     *
     * @return bool
     */
    public function isValid(string $uuid): bool
    {
        $pattern = '/[a-f0-9]{8}\-[a-f0-9]{4}\-4[a-f0-9]{3}\-(8|9|a|b)[a-f0-9]{3}\-[a-f0-9]{12}/';

        return uuid_is_valid($uuid) && preg_match($pattern, $uuid);
    }
}
