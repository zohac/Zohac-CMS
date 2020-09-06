<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Interfaces\EntityInterface;
use App\Repository\TranslationRepository;
use App\Traits\EntityTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ApiResource
 *
 * @ORM\Entity(repositoryClass=TranslationRepository::class)
 * @UniqueEntity("uuid")
 */
class Translation implements EntityInterface
{
    use EntityTrait;

    /**
     * @ORM\ManyToOne(targetEntity=Language::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $language;

    /**
     * @ORM\Column(type="text")
     */
    private $message;

    /**
     * @ORM\ManyToOne(targetEntity=Translatable::class, inversedBy="translations")
     * @ORM\JoinColumn(nullable=false)
     */
    private $translatable;

    public function getLanguage(): ?Language
    {
        return $this->language;
    }

    public function setLanguage(?Language $language): self
    {
        $this->language = $language;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

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
