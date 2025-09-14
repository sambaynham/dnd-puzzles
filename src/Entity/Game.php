<?php

namespace App\Entity;

use App\Repository\GameRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
#[ORM\Entity(repositoryClass: GameRepository::class)]
#[UniqueEntity(fields: ['slug'], message: 'There is already a game with this slug. Please choose another one.')]
class Game extends AbstractDomainEntity
{

    /**
     * @var Collection<int, GameInvitation>
     */
    #[ORM\OneToMany(targetEntity: GameInvitation::class, mappedBy: 'game', orphanRemoval: true)]
    private Collection $gameInvitations;

    public function __construct(
        #[ORM\Column(length: 255)]
        private string $name,

        #[ORM\Column(length: 255, unique: true)]
        private string $slug,

        #[ORM\Column(length: 1024)]
        private string $description,

        #[ORM\ManyToOne(inversedBy: 'gamesMastered')]
        #[ORM\JoinColumn(nullable: false)]
        private User $gamesMaster,

        #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'games')]
        private ?Collection $players = null,

        ?int $id = null
    ) {
        parent::__construct($id);
        $this->gameInvitations = new ArrayCollection();
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): void
    {
        $this->slug = $slug;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;

    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getGamesMaster(): User
    {
        return $this->gamesMaster;
    }

    public function setGamesMaster(User $gamesMaster): void
    {
        $this->gamesMaster = $gamesMaster;
    }

    /**
     * @return Collection<int, User>
     */
    public function getPlayers(): Collection
    {
        return $this->players;
    }

    public function addPlayer(User $player): void
    {
        if (!$this->players->contains($player)) {
            $this->players->add($player);
        }
    }

    public function removePlayer(User $player): void
    {
        $this->players->removeElement($player);
    }

    /**
     * @return Collection<int, GameInvitation>
     */
    public function getGameInvitations(): Collection
    {
        return $this->gameInvitations;
    }

    public function addGameInvitation(GameInvitation $gameInvitation): static
    {
        if (!$this->gameInvitations->contains($gameInvitation)) {
            $this->gameInvitations->add($gameInvitation);
            $gameInvitation->setGame($this);
        }

        return $this;
    }

    public function removeGameInvitation(GameInvitation $gameInvitation): static
    {
        if ($this->gameInvitations->removeElement($gameInvitation)) {
            // set the owning side to null (unless already changed)
            if ($gameInvitation->getGame() === $this) {
                $gameInvitation->setGame(null);
            }
        }

        return $this;
    }
}
