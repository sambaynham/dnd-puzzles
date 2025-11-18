<?php

namespace App\Services\Game\Domain;

use App\Entity\AbstractDomainEntity;
use App\Services\Game\Infrastructure\GameRepository;
use App\Services\Puzzle\Domain\PuzzleInstance;
use App\Services\User\Domain\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: GameRepository::class)]
#[UniqueEntity(fields: ['slug'], message: 'There is already a game with this slug. Please choose another one.')]
class Game extends AbstractDomainEntity
{

    /**
     * @var Collection<int, GameInvitation>
     */
    #[ORM\OneToMany(targetEntity: GameInvitation::class, mappedBy: 'game', orphanRemoval: true)]
    private Collection $gameInvitations;

    /**
     * @var Collection<int, PuzzleInstance>
     */
    #[ORM\OneToMany(targetEntity: PuzzleInstance::class, mappedBy: 'game', orphanRemoval: true)]
    private Collection $puzzleInstances;

    public function __construct(
        #[ORM\Column(length: 255)]
        #[Groups(['basic'])]
        private string $name,

        #[Groups(['basic'])]
        #[ORM\Column(length: 255, unique: true)]
        private string $slug,

        #[Groups(['extended'])]
        #[ORM\Column(length: 1024)]
        private string $description,

        #[ORM\ManyToOne(inversedBy: 'gamesMastered',fetch: 'EAGER')]
        #[ORM\JoinColumn(nullable: false)]
        #[Groups(['extended'])]
        private User $gamesMaster,

        #[Groups(['extended'])]
        #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'games')]
        private ?Collection $players = null,

        ?int $id = null
    ) {
        parent::__construct($id);
        $this->gameInvitations = new ArrayCollection();
        $this->puzzleInstances = new ArrayCollection();
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

    public function addGameInvitation(GameInvitation $gameInvitation): void
    {
        if (!$this->gameInvitations->contains($gameInvitation)) {
            $this->gameInvitations->add($gameInvitation);
            $gameInvitation->setGame($this);
        }
    }

    public function removeGameInvitation(GameInvitation $gameInvitation): void
    {
        if ($this->gameInvitations->removeElement($gameInvitation)) {
            // set the owning side to null (unless already changed)
            if ($gameInvitation->getGame() === $this) {
                $gameInvitation->setGame(null);
            }
        }
    }

    /**
     * @return Collection<int, PuzzleInstance>
     */
    public function getPuzzleInstances(): Collection
    {
        return $this->puzzleInstances;
    }

    public function addPuzzleInstances(PuzzleInstance $puzzleInstancesB): static
    {
        if (!$this->puzzleInstances->contains($puzzleInstancesB)) {
            $this->puzzleInstances->add($puzzleInstancesB);
            $puzzleInstancesB->setGame($this);
        }

        return $this;
    }

    public function removePuzzleInstances(PuzzleInstance $puzzleInstances): static
    {
        if ($this->puzzleInstances->removeElement($puzzleInstances)) {
            // set the owning side to null (unless already changed)
            if ($puzzleInstances->getGame() === $this) {
                $puzzleInstances->setGame(null);
            }
        }

        return $this;
    }
}
