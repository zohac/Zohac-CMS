<?php

namespace App\DependencyInjection\Manager;

use App\Entity\Parameter;
use Exception;

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
     *
     * @return Parameter
     *
     * @throws Exception
     */
    public function findOneByName(string $name): ?Parameter
    {
        $query = 'SELECT * FROM `parameter` WHERE `parameter`.`name` = :name';
        $parameterBag = $this->dataBaseConnection
            ->addQuery($query)
            ->setParameter(':name', $name, \PDO::PARAM_STR)
            ->execute();

        if (empty($parameterBag)) {
            return null;
        }

        $parameter = new Parameter();
        $parameter->setName($parameterBag['name'])
            ->setValue(['name' => $parameterBag['value']]);

        return $parameter;
    }
}
