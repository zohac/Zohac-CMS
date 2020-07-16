<?php

namespace App\Interfaces;

interface EntityInterface
{
    /**
     * @return string
     */
    public function getUuid(): ?string;

    /**
     * @return bool
     */
    public function isArchived(): bool;

    /**
     * @param bool $archived
     *
     * @return EntityInterface
     */
    public function setArchived(bool $archived): EntityInterface;
}
