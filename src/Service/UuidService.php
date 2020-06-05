<?php

namespace App\Service;

class UuidService
{
    /**
     * @return bool|string
     */
    public function create()
    {
        if (function_exists('uuid_create')) {
            $uuid = uuid_create(UUID_TYPE_RANDOM);

            $i = 0;
            if (!$this->isValid($uuid) && 5 < $i) {
                ++$i;
                $uuid = $this->create();
            }

            return $uuid;
        }

        return false;
    }

    /**
     * @param string $uuid
     *
     * @return bool
     */
    public function isValid(string $uuid): bool
    {
        return uuid_is_valid($uuid);
    }
}
