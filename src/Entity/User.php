<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use App\ApiResource\DiceRoll;
use App\Repository\UserRepository;
use App\Services\Puzzle\Domain\PuzzleTemplate;
use App\State\DiceStateProvider;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity("email")]
class User extends AbstractDomainEntity implements UserInterface, PasswordAuthenticatedUserInterface
{

    /**
     * @var Collection<int, Game>
     */
    #[ORM\OneToMany(targetEntity: Game::class, mappedBy: 'gamesMaster', orphanRemoval: true)]
    private Collection $gamesMastered;

    /**
     * @var Collection<int, Game>
     */
    #[ORM\ManyToMany(targetEntity: Game::class, mappedBy: 'players')]
    private Collection $games;

    public function __construct(
        #[ORM\Column(length: 255, type: 'string', unique: true)]
        #[Assert\Email]
        private string $email,

        #[ORM\Column(type: 'string', length: 255, nullable: false)]
        private string $username,

        #[ORM\Column(type: 'string', length: 255)]
        private string $password = '',

        #[ORM\Column]
        private array $roles = [],

        ? int $id = null
    ) {
        parent::__construct($id);
        if (empty($this->roles)) {
            $this->roles = ['ROLE_USER'];
        }
        $this->gamesMastered = new ArrayCollection();
        $this->games = new ArrayCollection();
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
}
