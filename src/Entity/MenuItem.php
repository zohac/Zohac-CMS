<?php

namespace App\Entity;

use App\Entity\HTMLElement\HTMLAnchorElement;
use App\Interfaces\EntityInterface;
use App\Repository\MenuItemRepository;
use App\Traits\EntityTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=MenuItemRepository::class)
 */
class MenuItem implements EntityInterface
{
    use EntityTrait;

    /**
     * @ORM\OneToOne(targetEntity=HTMLAnchorElement::class, cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $link;

    /**
     * @ORM\ManyToOne(targetEntity=MenuItem::class, inversedBy="items")
     */
    private $menuItem;

    /**
     * @ORM\OneToMany(targetEntity=MenuItem::class, mappedBy="menuItem")
     */
    private $items;

    /**
     * @ORM\ManyToOne(targetEntity=Menu::class, inversedBy="items")
     */
    private $menu;

    public function __construct()
    {
        $this->items = new ArrayCollection();
    }

    public function getLink(): ?HTMLAnchorElement
    {
        return $this->link;
    }

    public function setLink(HTMLAnchorElement $link): self
    {
        $this->link = $link;

        return $this;
    }

    public function getMenuItem(): ?self
    {
        return $this->menuItem;
    }

    public function setMenuItem(?self $menuItem): self
    {
        $this->menuItem = $menuItem;

        return $this;
    }

    /**
     * @return Collection|self[]
     */
    public function getItem(): Collection
    {
        return $this->items;
    }

    public function addItem(self $item): self
    {
        if (!$this->items->contains($item)) {
            $this->items[] = $item;
            $item->setMenuItem($this);
        }

        return $this;
    }

    public function removeItem(self $item): self
    {
        if ($this->items->contains($item)) {
            $this->items->removeElement($item);
            // set the owning side to null (unless already changed)
            if ($item->getMenuItem() === $this) {
                $item->setMenuItem(null);
            }
        }

        return $this;
    }

    public function getMenu(): ?Menu
    {
        return $this->menu;
    }

    public function setMenu(?Menu $menu): self
    {
        $this->menu = $menu;

        return $this;
    }
}
