<?php

declare(strict_types=1);

namespace App\Services\Puzzle\Domain\Casebook;

use App\Entity\AbstractDomainEntity;
use App\Services\Puzzle\Infrastructure\Casebook\CasebookRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: CasebookRepository::class)]
#[UniqueEntity(fields: ['slug'], message: 'There is already a casebook with this slug')]
class Casebook extends AbstractDomainEntity
{
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

        #[ORM\Column(length: 2048)]
        private string $brief,
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
}
