<?php

declare(strict_types=1);

namespace App\Services\Puzzle\Domain\Casebook;

use App\Services\Core\Domain\AbstractDomainEntity;
use App\Services\Game\Domain\Game;
use App\Services\Puzzle\Domain\Exceptions\MismappedPuzzleTemplateException;
use App\Services\Puzzle\Domain\Exceptions\PuzzleTemplateNotMappedException;
use App\Services\Puzzle\Domain\Interfaces\StaticPuzzleInstanceInterface;
use App\Services\Puzzle\Domain\PuzzleTemplate;
use App\Services\Puzzle\Infrastructure\Casebook\Repository\CasebookRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: CasebookRepository::class)]
#[UniqueEntity(fields: ['slug'], message: 'There is already a casebook with this slug')]
class Casebook extends AbstractDomainEntity implements StaticPuzzleInstanceInterface
{
    public const string TEMPLATE_SLUG = 'casebook';

    private ? PuzzleTemplate $puzzleTemplate = null;

    /**
     * @var Collection<int, CasebookSubject>
     */
    #[ORM\OneToMany(targetEntity: CasebookSubject::class, mappedBy: 'casebook', orphanRemoval: true)]
    private Collection $casebookSubjects;

    public function __construct(
        #[ORM\Column(length: 255)]
        private string $name,

        #[ORM\Column(length: 255, unique: true)]
        private string $slug,

        #[ORM\ManyToOne(targetEntity: Game::class)]
        private Game $game,

        #[ORM\Column(length: 2048)]
        private string $brief,

        #[ORM\Column(type: 'datetime', nullable: true)]
        private ?\DateTimeInterface $publicationDate = null,

        ? int $id = null
    ) {
        parent::__construct($id);
        $this->casebookSubjects = new ArrayCollection();
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): void
    {
        $this->slug = $slug;
    }

    public function getBrief(): string
    {
        return $this->brief;
    }

    public function setBrief(string $brief): void
    {
        $this->brief = $brief;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return Collection<int, CasebookSubject>
     */
    public function getCasebookSubjects(): Collection
    {
        return $this->casebookSubjects;
    }

    public function addCasebookSubject(CasebookSubject $casebookSubject): static
    {
        if (!$this->casebookSubjects->contains($casebookSubject)) {
            $this->casebookSubjects->add($casebookSubject);
            $casebookSubject->setCasebook($this);
        }

        return $this;
    }

    public function removeCasebookSubject(CasebookSubject $casebookSubject): static
    {
        if ($this->casebookSubjects->removeElement($casebookSubject)) {
            // set the owning side to null (unless already changed)
            if ($casebookSubject->getCasebook() === $this) {
                $casebookSubject->setCasebook(null);
            }
        }

        return $this;
    }

    public function getGame(): Game {
        return $this->game;
    }

    public function getInstanceCode(): string
    {
        return $this->slug;
    }

    public function setInstanceCode(string $instanceCode): static
    {
        $this->slug = $instanceCode;
    }

    public function getDescription(): string
    {
        return $this->brief;
    }

    public function setGame(Game $game): void
    {
        $this->game = $game;
    }

    public function getPublicationDate(): ?\DateTimeInterface
    {
        return $this->publicationDate;
    }

    public function setPublicationDate(\DateTimeInterface $publicationDate): void
    {
        $this->publicationDate = $publicationDate;
    }


    /**
     * @return string
     * @throws MismappedPuzzleTemplateException
     * @throws PuzzleTemplateNotMappedException
     */
    public function getTemplateSlug(): string
    {
        return self::TEMPLATE_SLUG;
    }

    public function isPublished(): bool
    {
        $date= new \DateTime();
        return $this->publicationDate !== null && $this->publicationDate >= $date;
    }

    public function getTemplate(): PuzzleTemplate
    {
        if (null === $this->puzzleTemplate) {
            throw new PuzzleTemplateNotMappedException();
        }
        if (self::TEMPLATE_SLUG !== $this->puzzleTemplate->getSlug()) {
            throw new MismappedPuzzleTemplateException();
        }
        return $this->puzzleTemplate;
    }

    public function setTemplate(PuzzleTemplate $puzzleTemplate): void
    {
        if (self::TEMPLATE_SLUG !== $puzzleTemplate->getSlug()) {
            throw new MismappedPuzzleTemplateException();
        }
        $this->puzzleTemplate = $puzzleTemplate;
    }

    public function getSubjectsByTypeHandle(string $typeHandle): ArrayCollection {
        return $this->casebookSubjects->filter(function (CasebookSubject $casebookSubject) use ($typeHandle) {
            return $casebookSubject->getCasebookSubjectType()->getHandle() === $typeHandle;
        });
    }
}
