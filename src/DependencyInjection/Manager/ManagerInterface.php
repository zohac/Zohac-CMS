<?php

namespace App\DependencyInjection\Manager;

use App\Entity\Parameter;

interface ManagerInterface
{
    /**
     * @return Parameter[]
     */
    public function findAll(): array;

    /**
     * @param string $name
     *
     * @return Parameter|null
     */
    public function findOneByName(string $name): ?Parameter;
}
