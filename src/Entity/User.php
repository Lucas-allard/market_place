<?php

namespace App\Entity;

use App\Entity\Interface\EntityInterface;
use App\Entity\Interface\UserInterface;
use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface as SymfonyUserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[UniqueEntity('email', message: "Un utilisateur ayant cette adresse email existe déjà !")]
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'user')]
#[ORM\InheritanceType('JOINED')]
#[ORM\DiscriminatorColumn(name: 'type', type: 'string')]
#[ORM\DiscriminatorMap(['customer' => 'Customer', 'seller' => 'Seller'])]
abstract class User extends AbstractEntity implements EntityInterface, UserInterface, SymfonyUserInterface, PasswordAuthenticatedUserInterface
{

    /**
     * @var string
     */
    #[ORM\Column(type: 'string', length: 50)]
    #[Assert\NotBlank(
        message: "Le prénom est obligatoire !",
        groups: ['registration']
    )]
    #[Assert\Length(
        min: 2,
        max: 50,
        minMessage: "Le prénom doit contenir au moins {{ limit }} caractères !",
        maxMessage: "Le prénom doit contenir au maximum {{ limit }} caractères !",
        groups: ['registration']
    )]
    private string $firstName = "";

    /**
     * @var string
     */
    #[ORM\Column(type: 'string', length: 50)]
    #[Assert\NotBlank(
        message: "Le nom est obligatoire !",
        groups: ['registration']
    )]
    #[Assert\Length(
        min: 2,
        max: 50,
        minMessage: "Le nom doit contenir au moins {{ limit }} caractères !",
        maxMessage: "Le nom doit contenir au maximum {{ limit }} caractères !",
        groups: ['registration']
    )]
    private string $lastName = "";

    /**
     * @var string
     */
    #[ORM\Column(type: 'string', length: 180, unique: true)]
    #[Assert\NotBlank(
        message: "L'adresse email est obligatoire !",
        groups: ['registration']
    )]
    #[Assert\Email(
        message: "L'adresse email n'est pas valide !",
        groups: ['registration']
    )]
    #[Assert\Length(
        min: 2,
        max: 180,
        minMessage: "L'adresse email doit contenir au moins {{ limit }} caractères !",
        maxMessage: "L'adresse email doit contenir au maximum {{ limit }} caractères !",
        groups: ['registration']
    )]
    private string $email = "";

    /**
     * @var string
     */
    #[ORM\Column(type: 'string', length: 255)]
    private string $password = "";

    /**
     * @var string
     */
    #[ORM\Column(type: 'string', length: 50)]
    #[Assert\NotBlank(message: "La ville est obligatoire !")]
    #[Assert\Length(
        min: 2,
        max: 50,
        minMessage: "La ville doit contenir au moins {{ limit }} caractères !",
        maxMessage: "La ville doit contenir au maximum {{ limit }} caractères !"
    )]
    private string $city = "";

    /**
     * @var string
     */
    #[ORM\Column(type: 'string', length: 50)]
    #[Assert\NotBlank(message: "Le nom de la rue est obligatoire !")]
    private string $street = "";

    /**
     * @var string
     */
    #[ORM\Column(type: 'string', length: 255)]
    private string $streetNumber = "";

    /**
     * @var string
     */
    #[ORM\Column(type: 'string', length: 5)]
    #[Assert\NotBlank(message: "Le code postal est obligatoire !")]
    #[Assert\Length(
        min: 5,
        max: 5,
        minMessage: "Le code postal doit contenir au moins {{ limit }} caractères !",
        maxMessage: "Le code postal doit contenir au maximum {{ limit }} caractères !"
    )]
    private string $postalCode = "";

    /**
     * @var string
     */
    #[ORM\Column(type: 'string', length: 10)]
    #[Assert\NotBlank(message: "Le numéro de téléphone est obligatoire !")]
    #[Assert\Regex(pattern: '/^\+33[67][0-9]{8}$/', message: "Le numéro de téléphone n'est pas valide !")]
    private string $phone = "";

    /**
     * @var string[]
     *
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var bool
     */
    #[ORM\Column(type: 'boolean')]
    private bool $isVerified = false;

    /**
     * @var string|null
     */
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $resetToken = null;

    public function __toString(): string
    {
        return $this->getFullName();
    }

    /**
     * @return string|null
     */
    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     * @return self
     */
    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     * @return self
     */
    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return self
     */
    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
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

    /**
     * @param string[] $roles
     * @return $this
     */
    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     * @return $this
     */
    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getCity(): ?string
    {
        return $this->city;
    }

    /**
     * @param string $city
     * @return User
     */
    public function setCity(string $city): User
    {
        $this->city = $city;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getStreet(): ?string
    {
        return $this->street;
    }

    /**
     * @param string $street
     * @return User
     */
    public function setStreet(string $street): User
    {
        $this->street = $street;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getStreetNumber(): ?string
    {
        return $this->streetNumber;
    }

    /**
     * @param string $streetNumber
     * @return User
     */
    public function setStreetNumber(string $streetNumber): User
    {
        $this->streetNumber = $streetNumber;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    /**
     * @param string $postalCode
     * @return User
     */
    public function setPostalCode(string $postalCode): User
    {
        $this->postalCode = $postalCode;
        return $this;
    }


    /**
     * @return string|null
     */
    public function getPhone(): ?string
    {
        return $this->phone;
    }

    /**
     * @param string $phone
     * @return User
     */
    public function setPhone(string $phone): User
    {
        $substr = substr($phone, 1);
        $this->phone = "+33" . $substr;
        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string)$this->email;
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
    public function eraseCredentials(): ?string
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
        return null;
    }

    /**
     * @return bool
     */
    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    /**
     * @param bool $isVerified
     * @return $this
     */
    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getResetToken(): ?string
    {
        return $this->resetToken;
    }

    /**
     * @param string|null $resetToken
     * @return $this
     */
    public function setResetToken(?string $resetToken): self
    {
        $this->resetToken = $resetToken;

        return $this;
    }

    /**
     * @return string
     */
    public function getFullName(): string
    {
        return $this->firstName . " " . $this->lastName;
    }

    public function getAddress(): string
    {
        return $this->streetNumber . ', ' . $this->street;
    }
}