<?php

namespace App\DependencyInjection\Manager;

use App\Entity\Parameter;

class ParameterManager implements ManagerInterface
{
    /**
     * @var DataBaseConnectionInterface
     */
    private $dataBaseConnection;

    public function __construct(DataBaseConnectionInterface $dataBaseConnection)
    {
        $this->dataBaseConnection = $dataBaseConnection;
    }

    /**
     * @return Parameter[]
     */
    public function findAll(): array
    {
        return [];
    }

    /**
     * @param string $name
     * @return Parameter
     */
    public function findOneByName(string $name): Parameter
    {
        $parameter = new Parameter();

        return $parameter;
    }
}