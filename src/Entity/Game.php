<?php

namespace App\Entity;

use App\Repository\GameRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GameRepository::class)]
class Game extends AbstractDomainEntity
{

    public function __construct(
        #[ORM\Column(length: 255)]
        private string $name,
        #[ORM\Column(length: 255)]
        private string $description,
        #[ORM\ManyToOne(inversedBy: 'gamesMastered')]
        #[ORM\JoinColumn(nullable: false)]
        private User $gamesMaster,
        #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'games')]
        private ?Collection $players = null,
        ?int $id = null
    ) {
        parent::__construct($id);
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
}
