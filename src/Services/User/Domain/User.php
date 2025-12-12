<?php

namespace App\Services\User\Domain;

use App\Entity\AbstractDomainEntity;
use App\Services\Game\Domain\Game;
use App\Services\User\Infrastructure\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\OrderBy;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(
    repositoryClass: UserRepository::class
)]
#[ORM\Index(name: 'username_idx', columns: ['username'])]
#[UniqueEntity("email")]
class User extends AbstractDomainEntity implements UserInterface, PasswordAuthenticatedUserInterface
{

    /**
     * @var Collection<int, Game>
     */
    #[ORM\OneToMany(targetEntity: Game::class, mappedBy: 'gamesMaster', orphanRemoval: true)]
    #[OrderBy(["createdAt" => "DESC"])]
    private Collection $gamesMastered;

    /**
     * @var Collection<int, Game>
     */
    #[ORM\ManyToMany(targetEntity: Game::class, mappedBy: 'players')]
    private Collection $games;

    #[ORM\ManyToMany(targetEntity: Role::class, inversedBy: 'users', cascade: ['persist', 'remove'], fetch: 'EAGER', indexBy: 'handle')]
    private Collection $roles;

    #[ORM\OneToOne(mappedBy: 'user', fetch: 'EAGER')]
    private ?UserBlock $userBlock = null;

    public function __construct(
        #[ORM\Column(type: 'string', length: 255, unique: true)]
        #[Assert\Email]
        private string $email,

        #[ORM\Column(type: 'string', length: 255, nullable: false)]
        private string $username,

        #[ORM\Column(type: 'string', length: 255)]
        private string $password = '',

        ? int $id = null
    ) {
        parent::__construct($id);
        $this->roles = new ArrayCollection();
        $this->gamesMastered = new ArrayCollection();
        $this->games = new ArrayCollection();
    }

    public function setRoles(Collection $roles): void
    {
        $this->roles = $roles;
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
        return $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        return array_map(function ($element) {
            return $element->getHandle();
        }, $this->roles->toArray());
    }


    /**
     * Ensure the session doesn't contain actual password hashes by CRC32C-hashing them, as supported since Symfony 7.3.
     */
    public function __serialize(): array
    {
        $data = (array) $this;
        $data["\0".self::class."\0password"] = hash('crc32c', $this->password);

        return $data;
    }

    #[\Deprecated]
    public function eraseCredentials(): void
    {
        // @deprecated, to be removed when upgrading to Symfony 8
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    /**
     * @return Collection<int, Game>
     */
    public function getGamesMastered(): Collection
    {
        return $this->gamesMastered;
    }

    public function addGamesMastered(Game $gamesMastered): static
    {
        if (!$this->gamesMastered->contains($gamesMastered)) {
            $this->gamesMastered->add($gamesMastered);
            $gamesMastered->setGamesMaster($this);
        }

        return $this;
    }

    public function removeGamesMastered(Game $gamesMastered): static
    {
        if ($this->gamesMastered->removeElement($gamesMastered)) {
            // set the owning side to null (unless already changed)
            if ($gamesMastered->getGamesMaster() === $this) {
                $gamesMastered->setGamesMaster(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Game>
     */
    public function getGames(): Collection
    {
        return $this->games;
    }

    public function addGame(Game $game): static
    {
        if (!$this->games->contains($game)) {
            $this->games->add($game);
            $game->addPlayer($this);
        }

        return $this;
    }

    public function removeGame(Game $game): static
    {
        if ($this->games->removeElement($game)) {
            $game->removePlayer($this);
        }

        return $this;
    }

    public function getHydratedRoles(): Collection {
        return $this->roles;
    }

    public function isBlocked(): bool
    {
        return $this->userBlock !== null;
    }

    public function getUserBlock(): ?UserBlock {
        return $this->userBlock;
    }
}
