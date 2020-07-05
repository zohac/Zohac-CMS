<?php

/*
 * ISO 639 is a standardized nomenclature used to classify languages.
 * Each language is assigned a two-letter (639-1) and three-letter (639-2 and 639-3) lowercase abbreviation,
 * amended in later versions of the nomenclature.
 * https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes
 */

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\LanguageRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=LanguageRepository::class)
 * @ApiResource
 */
class Language
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

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
    private $iso639_1;

    /**
     * @ORM\Column(type="string", length=3, nullable=true)
     */
    private $iso639_2T;

    /**
     * @ORM\Column(type="string", length=3, nullable=true)
     */
    private $iso639_2B;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $iso639_3 = [];

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
        return $this->iso639_1;
    }

    public function setIso6391(string $iso639_1): self
    {
        $this->iso639_1 = $iso639_1;

        return $this;
    }

    public function getIso6392T(): ?string
    {
        return $this->iso639_2T;
    }

    public function setIso6392T(string $iso639_2T): self
    {
        $this->iso639_2T = $iso639_2T;

        return $this;
    }

    public function getIso6392B(): ?string
    {
        return $this->iso639_2B;
    }

    public function setIso6392B(?string $iso639_2B): self
    {
        $this->iso639_2B = $iso639_2B;

        return $this;
    }

    public function getIso6393(): ?array
    {
        return $this->iso639_3;
    }

    public function setIso6393(?array $iso639_3): self
    {
        $this->iso639_3 = $iso639_3;

        return $this;
    }
}
