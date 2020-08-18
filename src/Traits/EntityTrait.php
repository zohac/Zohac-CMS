<?php

namespace App\Traits;

use App\Interfaces\User\AdvancedUserInterface;

/**
 * Trait EntityTrait.
 */
trait EntityTrait
{
    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @see AdvancedUserInterface
     */
    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    /**
     * @param string|null $uuid
     * @return $this
     */
    public function setUuid(?string $uuid): self
    {
        $this->uuid = $uuid;

        return $this;
    }

    /**
     * @return bool
     */
    public function isArchived(): bool
    {
        return $this->archived;
    }

    /**
     * @param bool $archived
     * @return $this
     */
    public function setArchived(bool $archived): self
    {
        $this->archived = $archived;

        return $this;
    }
}
