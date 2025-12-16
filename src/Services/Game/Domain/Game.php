<?php

namespace App\Services\Game\Domain;

use App\Services\Core\Domain\AbstractDomainEntity;
use App\Services\Game\Infrastructure\GameRepository;
use App\Services\Puzzle\Domain\Interfaces\PuzzleInstanceInterface;
use App\Services\Puzzle\Domain\PuzzleInstance;
use App\Services\User\Domain\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\OrderBy;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: GameRepository::class)]
#[UniqueEntity(fields: ['slug'], message: 'There is already a game with this slug. Please choose another one.')]
#[ORM\Index(name: 'gm_id', columns: ['games_master_id'])]
class Game extends AbstractDomainEntity
{

    /**
     * @var Collection<int, GameInvitation>
     */
    #[ORM\OneToMany(targetEntity: GameInvitation::class, mappedBy: 'game', orphanRemoval: true)]
    #[OrderBy(["createdAt" => "DESC"])]
    private Collection $gameInvitations;

    /**
     * @var Collection<int, PuzzleInstance>
     */
    #[ORM\OneToMany(targetEntity: PuzzleInstance::class, mappedBy: 'game', fetch: 'EAGER', orphanRemoval: true)]
    #[OrderBy(["createdAt" => "DESC"])]
    private Collection $dynamicPuzzleInstances;

    private Collection $staticPuzzleInstances;

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

        #[ORM\ManyToOne(inversedBy: 'gamesMastered')]
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
        $this->dynamicPuzzleInstances = new ArrayCollection();
        $this->staticPuzzleInstances = new ArrayCollection();
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

    /**
     * @return Collection<int, PuzzleInstance>
     */
    public function getDynamicPuzzleInstances(): Collection
    {
        return $this->dynamicPuzzleInstances;
    }

    public function addDynamicPuzzleInstances(PuzzleInstance $dynamicPuzzleInstance): static
    {
        if (!$this->dynamicPuzzleInstances->contains($dynamicPuzzleInstance)) {
            $this->dynamicPuzzleInstances->add($dynamicPuzzleInstance);
            $dynamicPuzzleInstance->setGame($this);
        }

        return $this;
    }

    public function removeDynamicPuzzleInstances(PuzzleInstance $dynamicPuzzleInstance): static
    {
        if ($this->dynamicPuzzleInstances->removeElement($dynamicPuzzleInstance)) {
            // set the owning side to null (unless already changed)
            if ($dynamicPuzzleInstance->getGame() === $this) {
                $dynamicPuzzleInstance->setGame(null);
            }
        }

        return $this;
    }

    public function setStaticPuzzleInstances(ArrayCollection $staticPuzzles): void
    {
        $this->staticPuzzleInstances = $staticPuzzles;
    }

    public function getStaticPuzzleInstances(): Collection {
        return $this->staticPuzzleInstances;
    }

    public function getPuzzleInstances(): Collection {
        /**
         * @var ArrayCollection<PuzzleInstanceInterface> $puzzlesInstances
         */
        $puzzlesInstances = new ArrayCollection();
        foreach ($this->getDynamicPuzzleInstances() as $instance) {
            $puzzlesInstances->add($instance);
        }

        foreach ($this->getStaticPuzzleInstances() as $instance) {
            $puzzlesInstances->add($instance);
        }
        return $puzzlesInstances;
    }

    public function getPublishedPuzzleInstances(): Collection {
        return $this->getPuzzleInstances()->filter(function ($instance) {
            return $instance->isPublished();
        });
    }
}
