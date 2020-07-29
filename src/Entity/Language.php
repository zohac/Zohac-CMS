<?php

/*
 * ISO 639 is a standardized nomenclature used to classify languages.
 * Each language is assigned a two-letter (639-1) and three-letter (639-2 and 639-3) lowercase abbreviation,
 * amended in later versions of the nomenclature.
 * https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes
 */

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Interfaces\EntityInterface;
use App\Repository\LanguageRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=LanguageRepository::class)
 * @ApiResource
 */
class Language implements EntityInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ApiProperty(identifier=true)
     *
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $uuid;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $alternateName;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=2)
     */
    private $iso6391;

    /**
     * @ORM\Column(type="string", length=3, nullable=true)
     */
    private $iso6392T;

    /**
     * @ORM\Column(type="string", length=3, nullable=true)
     */
    private $iso6392B;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $iso6393 = [];

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $archived;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getAlternateName(): ?string
    {
        return $this->alternateName;
    }

    public function setAlternateName(string $alternateName): self
    {
        $this->alternateName = $alternateName;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getIso6391(): ?string
    {
        return $this->iso6391;
    }

    public function setIso6391(string $iso6391): self
    {
        $this->iso6391 = $iso6391;

        return $this;
    }

    public function getIso6392T(): ?string
    {
        return $this->iso6392T;
    }

    public function setIso6392T(?string $iso6392T): self
    {
        $this->iso6392T = $iso6392T;

        return $this;
    }

    public function getIso6392B(): ?string
    {
        return $this->iso6392B;
    }

    public function setIso6392B(?string $iso6392B): self
    {
        $this->iso6392B = $iso6392B;

        return $this;
    }

    public function getIso6393(): ?array
    {
        return $this->iso6393;
    }

    public function setIso6393(?array $iso6393): self
    {
        $this->iso6393 = $iso6393;

        return $this;
    }

    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    public function setUuid(string $uuid): self
    {
        $this->uuid = $uuid;

        return $this;
    }

    public function isArchived(): bool
    {
        return $this->archived;
    }

    public function setArchived(bool $archived): self
    {
        $this->archived = $archived;

        return $this;
    }
}
