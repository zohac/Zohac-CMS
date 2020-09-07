<?php

namespace App\Entity;

use App\Interfaces\EntityInterface;
use App\Repository\MaintenanceRepository;
use App\Traits\EntityTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=MaintenanceRepository::class)
 */
class Maintenance implements EntityInterface
{
    use EntityTrait;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $redirectPath;

    /**
     * @ORM\Column(type="boolean")
     */
    private $mode;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $ips = [];

    public function getRedirectPath(): ?string
    {
        return $this->redirectPath;
    }

    public function setRedirectPath(?string $redirectPath): self
    {
        $this->redirectPath = $redirectPath;

        return $this;
    }

    public function getMode(): ?bool
    {
        return $this->mode;
    }

    public function setMode(bool $mode): self
    {
        $this->mode = $mode;

        return $this;
    }

    public function getIps(): ?array
    {
        return $this->ips;
    }

    public function setIps(?array $ips): self
    {
        $this->ips = $ips;

        return $this;
    }
}
