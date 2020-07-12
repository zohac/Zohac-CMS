<?php

namespace App\Interfaces\Factory;

interface EntityFactoryInterface
{
    /**
     * @return string
     */
    public function getRelatedEntity(): string;
}
