<?php

namespace App\Services\Puzzle\Domain\Casebook;

use App\Services\Core\Domain\AbstractDomainEntity;
use App\Services\Puzzle\Infrastructure\Casebook\Repository\CasebookSubjectClueRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CasebookSubjectClueRepository::class)]
/**
 * @implements \ArrayAccess<string, mixed>
 */
class CasebookSubjectClue extends AbstractDomainEntity implements \ArrayAccess
{
    private const array ARRAY_ACCESSIBLE_PROPERTIES = [
        'title',
        'body',
        'type',
        'revealedDate'
    ];

    final public function __construct(
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
        switch ($offset) {
            case 'title':
                if (!is_string($value)) {
                    throw new \Exception('Title must be a string');
                } else {
                    $this->setTitle($value);
                }
                break;
            case 'body':
                if (!is_string($value)) {
                    throw new \Exception('Body must be a string');
                } else {
                    $this->setBody($value);
                }
                break;
            case 'type':
                if (!$value instanceof CasebookSubjectClueType) {
                    throw new \Exception('Type must be an instance of CasebookSubjectClueType');
                } else {
                    $this->setType($value);
                }
                break;
            case 'revealedDate':
                break;
            default:
                if (is_string($offset)) {
                    throw new \Exception(sprintf("Unknown offset %s",  $offset));
                } else {
                    throw new \Exception(sprintf("Unprocessable offset type %s", gettype($offset)));
                }
                break;

        }

    }

    public function offsetUnset(mixed $offset): void
    {
        throw new \Exception();
    }
}
