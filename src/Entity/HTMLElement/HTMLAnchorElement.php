<?php

namespace App\Entity\HTMLElement;

use App\Repository\HTMLAnchorElementRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=HTMLAnchorElementRepository::class)
 */
class HTMLAnchorElement
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $uuid;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $href;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $target;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $download;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $rel;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $hreflang;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $referrerpolicy;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $content;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getHref(): ?string
    {
        return $this->href;
    }

    public function setHref(?string $href): self
    {
        $this->href = $href;

        return $this;
    }

    public function getTarget(): ?string
    {
        return $this->target;
    }

    public function setTarget(?string $target): self
    {
        $this->target = $target;

        return $this;
    }

    public function getDownload(): ?string
    {
        return $this->download;
    }

    public function setDownload(?string $download): self
    {
        $this->download = $download;

        return $this;
    }

    public function getRel(): ?string
    {
        return $this->rel;
    }

    public function setRel(?string $rel): self
    {
        $this->rel = $rel;

        return $this;
    }

    public function getHreflang(): ?string
    {
        return $this->hreflang;
    }

    public function setHreflang(?string $hreflang): self
    {
        $this->hreflang = $hreflang;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getReferrerpolicy(): ?string
    {
        return $this->referrerpolicy;
    }

    public function setReferrerpolicy(?string $referrerpolicy): self
    {
        $this->referrerpolicy = $referrerpolicy;

        return $this;
    }

    /**
     * @return string
     */
    public function getUuid(): string
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
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param string|null $content
     *
     * @return $this
     */
    public function setContent(?string $content): self
    {
        $this->content = $content;

        return $this;
    }
}
