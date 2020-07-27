<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Interfaces\EntityInterface;
use App\Interfaces\User\AdvancedUserInterface;
use App\Repository\UserRepository;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ApiResource
 *
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity("uuid")
 */
class User implements AdvancedUserInterface, EntityInterface
{
    /**
     * The unique auto incremented primary key.
     *
     * @var int|null
     * @ApiProperty(identifier=false)
     *
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer", options={"unsigned": true})
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
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @var string|null a token
     * @ORM\Column(type="string", length=255, nullable=true, unique=true)
     */
    private $token;

    /**
     * @var DateTimeInterface|null
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $tokenValidity;

    /**
     * @ORM\Column(type="boolean")
     */
    private $archived = false;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $locale;

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

    public function setUuid(?string $uuid): self
    {
        $this->uuid = $uuid;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
    }

    /**
     * @see AdvancedUserInterface
     */
    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(?string $token): self
    {
        $this->token = $token;

        return $this;
    }

    /**
     * @see AdvancedUserInterface
     */
    public function getTokenValidity(): ?DateTimeInterface
    {
        return $this->tokenValidity;
    }

    public function setTokenValidity(?DateTimeInterface $tokenValidity): self
    {
        $this->tokenValidity = $tokenValidity;

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

    public function getLocale(): ?string
    {
        return $this->locale;
    }

    public function setLocale(string $locale): self
    {
        $this->locale = $locale;

        return $this;
    }
}
