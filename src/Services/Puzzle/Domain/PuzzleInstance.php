<?php

namespace App\Services\Puzzle\Domain;

use App\Services\Core\Domain\AbstractDomainEntity;
use App\Services\Game\Domain\Game;
use App\Services\Puzzle\Domain\Exceptions\PuzzleTemplateNotMappedException;
use App\Services\Puzzle\Domain\Interfaces\PuzzleInstanceInterface;
use App\Services\Puzzle\Infrastructure\PuzzleInstanceRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PuzzleInstanceRepository::class)]
class PuzzleInstance extends AbstractDomainEntity implements PuzzleInstanceInterface
{
    private ? PuzzleTemplate $puzzleTemplate = null;

    public function __construct(
        #[ORM\Column(length: 255, unique: true)]
        private string $instanceCode,

        #[ORM\Column(length: 255)]
        private string $name,

        #[ORM\Column(length: 2048)]
        private string $description,

        #[ORM\ManyToOne(inversedBy: 'dynamicPuzzleInstances')]
        #[ORM\JoinColumn(nullable: false)]
        private Game $game,

        #[ORM\Column(length:1024)]
        private string $templateSlug,

        #[ORM\Column(type: 'datetime', nullable: true)]
        private ?\DateTimeInterface $publicationDate = null,

        /**
         * @var array<int, mixed>
         */
        #[ORM\Column]
        private array $config = [],

        ?int $id = null
    ){
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

    public function getInstanceCode(): string
    {
        return $this->instanceCode;
    }

    public function setInstanceCode(string $instanceCode): void
    {
        $this->instanceCode = $instanceCode;
    }

    public function getGame(): Game
    {
        return $this->game;
    }

    public function setGame(Game $game): void
    {
        $this->game = $game;
    }

    /**
     * @return mixed[]
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    public function setConfig(array $config): static
    {
        $this->config = $config;

        return $this;
    }

    public function getPublicationDate(): ?\DateTimeInterface
    {
        return $this->publicationDate;
    }

    public function setPublicationDate(\DateTimeInterface $publicationDate): void
    {
        $this->publicationDate = $publicationDate;
    }

    public function getTemplateSlug(): string
    {
        return $this->templateSlug;
    }

    public function isPublished(): bool
    {
        $date= new \DateTime();
        return $this->publicationDate !== null && $this->publicationDate >= $date;
    }

    /**
     * @throws PuzzleTemplateNotMappedException
     */
    public function getTemplate(): PuzzleTemplate
    {
        if ($this->puzzleTemplate === null) {
            throw new PuzzleTemplateNotMappedException();
        }
        return $this->puzzleTemplate;
    }

    public function setTemplate(PuzzleTemplate $puzzleTemplate): void
    {
        $this->puzzleTemplate = $puzzleTemplate;
    }
}
