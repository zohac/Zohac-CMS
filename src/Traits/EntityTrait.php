<?php

namespace App\Traits;

use ApiPlatform\Core\Annotation\ApiProperty;
use App\Interfaces\User\AdvancedUserInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * Trait EntityTrait.
 */
trait EntityTrait
{
    /**
     * The unique auto incremented primary key.
     *
     * @var int|null
     * @ApiProperty(identifier=false)
     *
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer", options={"unsigned"=true})
     */
    private $id;

    /**
     * @var string
     * @ApiProperty(identifier=true)
     *
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $uuid;

    /**
     * @ORM\Column(type="boolean")
     */
    private $archived = false;

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
     *
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
     *
     * @return $this
     */
    public function setArchived(bool $archived): self
    {
        $this->archived = $archived;

        return $this;
    }
}
