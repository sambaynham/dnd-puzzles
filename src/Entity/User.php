<?php

namespace App\Entity;

use App\Dto\UserDto;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_USERNAME', fields: ['username'])]
#[UniqueEntity(fields: ['username'], message: 'There is already an account with this username')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{

    #[ORM\Column]
    private bool $isVerified = false;

    public function __construct(
        #[ORM\Column(length: 180)]
        #[Assert\NotBlank]
        private string $username,

        #[ORM\Column(type: 'string', unique: true)]
        #[Assert\Email]
        #[Assert\NotBlank]
        private string $emailAddress,

        #[ORM\Column]
        #[Assert\NotBlank]
        private string $password = '',

        #[ORM\Column]
        private array $roles = [],

        #[ORM\OneToMany(
            mappedBy: 'author',
            targetEntity: PuzzleTemplate::class,
            orphanRemoval: false,
        )]
        private Collection $puzzlesAuthored = new ArrayCollection(),

        #[ORM\Id]
        #[ORM\GeneratedValue]
        #[ORM\Column]
        private ?int $id = null
    ) {}

    public function getPuzzlesAuthored(): Collection
    {
        return $this->puzzlesAuthored;
    }

    public function setPuzzlesAuthored(Collection $puzzlesAuthored): void
    {
        $this->puzzlesAuthored = $puzzlesAuthored;
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return $this->username;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): void
    {
        $this->roles = $roles;
    }

    public function getEmailAddress(): string {
        return $this->emailAddress;
    }

    public function setEmailAddress(string $emailAddress): void {
        $this->emailAddress = $emailAddress;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }
}
