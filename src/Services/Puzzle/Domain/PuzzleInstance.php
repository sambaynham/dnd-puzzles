<?php

namespace App\Services\Puzzle\Domain;

use App\Services\Game\Domain\Game;
use App\Services\Puzzle\Infrastructure\PuzzleInstanceRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PuzzleInstanceRepository::class)]
class PuzzleInstance
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $instanceCode = null;

    #[ORM\ManyToOne(inversedBy: 'puzzleInstancesB')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Game $game = null;

    #[ORM\Column]
    private array $config = [];

    #[ORM\Column(nullable: true)]
    private ?\DateTime $publicationDate = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getInstanceCode(): ?string
    {
        return $this->instanceCode;
    }

    public function setInstanceCode(string $instanceCode): static
    {
        $this->instanceCode = $instanceCode;

        return $this;
    }

    public function getGame(): ?Game
    {
        return $this->game;
    }

    public function setGame(?Game $game): static
    {
        $this->game = $game;

        return $this;
    }

    public function getConfig(): array
    {
        return $this->config;
    }

    public function setConfig(array $config): static
    {
        $this->config = $config;

        return $this;
    }

    public function getPublicationDate(): ?\DateTime
    {
        return $this->publicationDate;
    }

    public function setPublicationDate(?\DateTime $publicationDate): static
    {
        $this->publicationDate = $publicationDate;

        return $this;
    }
}
