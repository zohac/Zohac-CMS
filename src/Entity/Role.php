<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Interfaces\EntityInterface;
use App\Repository\RoleRepository;
use App\Traits\EntityTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ApiResource
 *
 * @ORM\Entity(repositoryClass=RoleRepository::class)
 * @UniqueEntity("uuid")
 */
class Role implements EntityInterface
{
    use EntityTrait;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ApiProperty(identifier=true)
     *
     * @ORM\Column(type="string", length=255)
     */
    private $uuid;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\OneToOne(targetEntity=Translatable::class, cascade={"persist", "remove"})
     */
    private $translatable;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $archived;

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getTranslatable(): ?Translatable
    {
        return $this->translatable;
    }

    public function setTranslatable(?Translatable $translatable): self
    {
        $this->translatable = $translatable;

        return $this;
    }
}
