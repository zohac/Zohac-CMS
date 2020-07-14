<?php

namespace App\Interfaces;

interface EntityInterface
{
    /**
     * @return string
     */
    public function getUuid(): ?string;

    /**
     * @return bool|null
     */
    public function isArchived(): ?bool;
}
