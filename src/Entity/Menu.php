<?php

namespace App\Entity;

use App\Interfaces\EntityInterface;
use App\Repository\MenuRepository;
use App\Traits\EntityTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=MenuRepository::class)
 */
class Menu implements EntityInterface
{
    use EntityTrait;

    /**
     * @ORM\OneToMany(targetEntity=MenuItem::class, mappedBy="menu")
     */
    private $items;

    public function __construct()
    {
        $this->items = new ArrayCollection();
    }

    /**
     * @return Collection|MenuItem[]
     */
    public function getItems(): Collection
    {
        return $this->items;
    }

    public function addItem(MenuItem $item): self
    {
        if (!$this->items->contains($item)) {
            $this->items[] = $item;
            $item->setMenu($this);
        }

        return $this;
    }

    public function removeItem(MenuItem $item): self
    {
        if ($this->items->contains($item)) {
            $this->items->removeElement($item);
            // set the owning side to null (unless already changed)
            if ($item->getMenu() === $this) {
                $item->setMenu(null);
            }
        }

        return $this;
    }
}
