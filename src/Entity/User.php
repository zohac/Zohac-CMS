<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Interfaces\EntityInterface;
use App\Interfaces\User\AdvancedUserInterface;
use App\Repository\UserRepository;
use App\Traits\EntityTrait;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
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
    use EntityTrait;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\ManyToMany(targetEntity=Role::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $roles;

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
     * @ORM\ManyToOne(targetEntity=Language::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $language;

    public function __construct()
    {
        $this->roles = new ArrayCollection();
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
        $rolesName = [];

        /** @var Role $role */
        foreach ($this->roles as $role) {
            $rolesName[] = $role->getName();
        }

        // guarantee every user at least has ROLE_USER
        $rolesName[] = 'ROLE_USER';

        return array_unique($rolesName);
    }

    /**
     * @return ArrayCollection
     */
    public function getRolesEntities()
    {
        return $this->roles;
    }

    public function addRole(Role $role): self
    {
        if (!$this->roles->contains($role)) {
            $this->roles[] = $role;
        }

        return $this;
    }

    public function removeRole(Role $role): self
    {
        if ($this->roles->contains($role)) {
            $this->roles->removeElement($role);
        }

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

    /**
     * @return string
     */
    public function getLocale(): string
    {
        return $this->language->getIso6391();
    }

    /**
     * @return Language|null
     */
    public function getLanguage(): ?Language
    {
        return $this->language;
    }

    /**
     * @param Language|null $language
     *
     * @return $this
     */
    public function setLanguage(?Language $language): self
    {
        $this->language = $language;

        return $this;
    }
}
