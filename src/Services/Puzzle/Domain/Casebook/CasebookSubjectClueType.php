<?php

namespace App\Services\Puzzle\Domain\Casebook;

use App\Services\Puzzle\Infrastructure\Casebook\CasebookSubjectClueTypeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CasebookSubjectClueTypeRepository::class)]
class CasebookSubjectClueType
{
    public function __construct(
        #[ORM\Column(length: 255)]
        private readonly string $name,
        #[ORM\Column(length: 255, unique: true)]
        private readonly string $handle,
        #[ORM\Id]
        #[ORM\GeneratedValue]
        #[ORM\Column]
        private ? int $id = null
    ) {}

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getHandle(): string
    {
        return $this->handle;
    }
}
