<?php

namespace App\Services\Puzzle\Domain\Casebook;

use App\Services\Core\Domain\AbstractDomainEntity;
use App\Services\Puzzle\Infrastructure\Casebook\Repository\CasebookSubjectClueRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CasebookSubjectClueRepository::class)]
class CasebookSubjectClue extends AbstractDomainEntity implements \ArrayAccess
{

    private const array ARRAY_ACCESSIBLE_PROPERTIES = [
        'title',
        'body',
        'type',
        'revealedDate'
    ];

    public function __construct(
        #[ORM\Column(length: 255)]
        private string $title,

        #[ORM\Column(length: 1024)]
        private string $body,

        #[ORM\ManyToOne(targetEntity: CasebookSubjectClueType::class)]
        private CasebookSubjectClueType $type,

        #[ORM\ManyToOne(inversedBy: 'casebookSubjectClues')]
        #[ORM\JoinColumn(nullable: false)]
        private CasebookSubject $casebookSubject,

        #[ORM\Column(nullable: true, type: 'datetime_immutable')]
        private ? \DateTimeInterface $revealedDate = null,
        ?int $id = null
    ) {
        parent::__construct($id);
    }

    public function setType(CasebookSubjectClueType $type): void {
        $this->type = $type;
    }

    public function getType(): CasebookSubjectClueType
    {
        return $this->type;
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

    public function offsetExists(mixed $offset): bool
    {
        return in_array($offset, self::ARRAY_ACCESSIBLE_PROPERTIES, true);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return match ($offset) {
            'title' => $this->getTitle(),
            'body' => $this->getBody(),
            'type' => $this->getType(),
            'revealedDate' => $this->getRevealedDate(),
            default => null,
        };

    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        // TODO: Implement offsetSet() method.
    }

    public function offsetUnset(mixed $offset): void
    {
        // TODO: Implement offsetUnset() method.
    }
}
