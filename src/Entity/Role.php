<?php

namespace App\Entity;

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
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\OneToOne(targetEntity=Translatable::class, cascade={"persist", "remove"})
     */
    private $translatable;

    /**
     * @ORM\ManyToOne(targetEntity=Role::class)
     */
    private $parent;

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

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setParent(?self $parent): self
    {
        $this->parent = $parent;

        return $this;
    }
}
