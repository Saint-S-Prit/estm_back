<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\InheritanceType;
use Doctrine\ORM\Mapping\DiscriminatorColumn;
use Doctrine\ORM\Mapping\DiscriminatorMap;
use App\Repository\UserRepository;
use Symfony\Component\Security\Core\User\UserInterface;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(name="`user`")
 * @InheritanceType("JOINED")
 * @DiscriminatorColumn(name="user_role", type="string")
 * @DiscriminatorMap({"user"="User","administrateur" = "Administrateur", "agent" = "Agent", "etudiant" = "Etudiant", "superviseur" = "Superviseur"})
 *  @ApiResource(
 *  normalizationContext={"groups"={"user_write"}},
 *  denormalizationContext={"groups"={"user_read"}},
 *      collectionOperations={
 *          "POST" = {"path" = "/user"},
 *          "GET" = {"path" = "/user"},
 *      },
 *      itemOperations = {
 *          "GET" = {"path" = "/user/{id}"},
 *      }
 * )
 */

class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Groups({"user_write","user_read"})
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     *  @Groups({"user_write"})
     */
    private $password = "ESTM-2021";

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"user_write","user_read"})
     */
    private $prenom;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"user_write","user_read"})

     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"user_write","user_read"})
     * 
     */
    private $adresse;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"user_write","user_read"})
     */
    private $telephone;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"user_write","user_read"})
     */
    private $sexe;

    /**
     * @ORM\Column(type="blob", nullable=true)
     * @Groups({"user_write"})
     */
    private $avatar;

    /**
     * @ORM\ManyToOne(targetEntity=Profile::class, inversedBy="user")
     * @Groups({"user_write","user_read"})
     */
    private $profile;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"user_write","user_read"})
     */
    private $isDeleted = false;


    public function getId(): ?int
    {
        return $this->id;
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
        $roles[] = 'ROLE_' . strtoupper($this->getProfile()->getLibelle());

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
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): self
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(string $telephone): self
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getSexe(): ?string
    {
        return $this->sexe;
    }

    public function setSexe(string $sexe): self
    {
        $this->sexe = $sexe;

        return $this;
    }

    public function getAvatar()
    {
        if ($this->avatar != null) {
            return $this->avatar != null ? \base64_encode(stream_get_contents($this->avatar)) : null;
        }
    }

    public function setAvatar($avatar): self
    {
        $this->avatar = $avatar;

        return $this;
    }

    public function getProfile(): ?Profile
    {
        return $this->profile;
    }

    public function setProfile(?Profile $profile): self
    {
        $this->profile = $profile;

        return $this;
    }

    public function getIsDeleted(): ?bool
    {
        return $this->isDeleted;
    }

    public function setIsDeleted(bool $isDeleted): self
    {
        $this->isDeleted = $isDeleted;

        return $this;
    }
}
