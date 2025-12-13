<?php

declare(strict_types=1);

namespace App\Services\Core\Domain;

use Doctrine\ORM\Mapping as ORM;

#[ORM\MappedSuperclass]
#[ORM\HasLifecycleCallbacks]
abstract class AbstractDomainEntity
{
    #[ORM\Column(type: 'datetime_immutable')]
    private ? \DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private ? \DateTimeImmutable $updatedAt = null;

    public function __construct(
        #[ORM\Id]
        #[ORM\GeneratedValue]
        #[ORM\Column]
        private ?int $id = null
    ) {}

    final public function getId(): ?int {
        return $this->id ?? null;
    }

    final public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getCreatedAt(): ? \DateTimeImmutable {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ? \DateTimeImmutable {
        return $this->updatedAt;
    }

    #[ORM\PrePersist]
    public function prePersist(): void {
        $persistTime = new \DateTimeImmutable();
        $this->createdAt = $persistTime;
        $this->updatedAt = $persistTime;

    }

    #[ORM\PreUpdate]
    public function preUpdate(): void {
        $this->updatedAt = new \DateTimeImmutable();
    }

}
