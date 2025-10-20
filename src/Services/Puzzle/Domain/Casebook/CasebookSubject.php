<?php

namespace App\Services\Puzzle\Domain\Casebook;

use App\Entity\AbstractDomainEntity;
use App\Repository\CasebookSubjectRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CasebookSubjectRepository::class)]
class CasebookSubject extends AbstractDomainEntity
{

    public function __construct(
        #[ORM\Column(length: 255)]
        private string $name,

        #[ORM\Column(length: 2048)]
        private string $description,

        #[ORM\ManyToOne(inversedBy: 'casebookSubjects')]
        #[ORM\JoinColumn(nullable: false)]
        private Casebook $casebook,

        /**
         * @var Collection<int, CasebookSubjectClue>
         */
        #[ORM\OneToMany(targetEntity: CasebookSubjectClue::class, mappedBy: 'casebookSubject', orphanRemoval: true)]
        private Collection $casebookSubjectClues,

        ?int $id = null
    ) {
        parent::__construct($id);
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }


    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getCasebook(): Casebook
    {
        return $this->casebook;
    }

    public function setCasebook(Casebook $casebook): void
    {
        $this->casebook = $casebook;
    }

    /**
     * @return Collection<int, CasebookSubjectClue>
     */
    public function getCasebookSubjectClues(): Collection
    {
        return $this->casebookSubjectClues;
    }

    public function addCasebookSubjectClue(CasebookSubjectClue $casebookSubjectClue): void
    {
        if (!$this->casebookSubjectClues->contains($casebookSubjectClue)) {
            $this->casebookSubjectClues->add($casebookSubjectClue);
            $casebookSubjectClue->setCasebookSubject($this);
        }
    }
}
