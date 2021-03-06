<?php

namespace App\DependencyInjection\Manager;

use Exception;

interface DataBaseConnectionInterface
{
    /**
     * @param string $query
     *
     * @return $this
     */
    public function addQuery(string $query): self;

    /**
     * @param string $parameter
     * @param $value
     * @param $option
     *
     * @return $this
     */
    public function setParameter(string $parameter, $value, $option = null): self;

    /**
     * @return iterable
     *
     * @throws Exception
     */
    public function execute(): iterable;
}
