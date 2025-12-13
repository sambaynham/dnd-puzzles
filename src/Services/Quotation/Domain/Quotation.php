<?php

namespace App\Services\Quotation\Domain;

use App\Services\Core\Domain\AbstractDomainEntity;
use App\Services\Quotation\Infrastructure\QuotationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: QuotationRepository::class)]
class Quotation extends AbstractDomainEntity
{
    public function __construct(
        #[ORM\Column(length: 1024)]
        private string $quotation,

        #[ORM\Column(length: 255)]
        private string $citation,
        ?int $id = null
    ) {
        parent::__construct($id);
    }

    public function getQuotation(): string
    {
        return $this->quotation;
    }

    public function setQuotation(string $quotation): void
    {
        $this->quotation = $quotation;
    }

    public function getCitation(): string
    {
        return $this->citation;
    }

    public function setCitation(string $citation): void
    {
        $this->citation = $citation;

    }
}
