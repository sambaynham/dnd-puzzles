<?php

namespace App\Services\Puzzle\Domain\Casebook;

use App\Entity\AbstractDomainEntity;
use App\Services\Puzzle\Infrastructure\Casebook\CasebookSubjectClueRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CasebookSubjectClueRepository::class)]
class CasebookSubjectClue extends AbstractDomainEntity
{
    public function __construct(
        #[ORM\Column(length: 255)]
        private string $title,
        #[ORM\Column(length: 1024)]
        private string $body,
        #[ORM\ManyToOne(inversedBy: 'casebookSubjectClues')]
        #[ORM\JoinColumn(nullable: false)]
        private CasebookSubject $casebookSubject,
        #[ORM\Column(nullable: true)]
        private ? \DateTimeInterface $revealedDate = null,
        ?int $id = null
    ) {
        parent::__construct($id);
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function setBody(string $body): void
    {
        $this->body = $body;
    }

    public function setCasebookSubject(CasebookSubject $casebookSubject): void {
        $this->casebookSubject = $casebookSubject;
    }
    public function getCasebookSubject(): CasebookSubject
    {
        return $this->casebookSubject;
    }

    public function getRevealedDate(): ?\DateTimeInterface
    {
        return $this->revealedDate;
    }

    public function reveal(): void {
        $this->revealedDate = new \DateTimeImmutable();
    }
}
