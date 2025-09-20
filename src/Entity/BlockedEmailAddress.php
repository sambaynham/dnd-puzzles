<?php

namespace App\Entity;

use App\Repository\BlockedEmailAddressRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BlockedEmailAddressRepository::class)]
class BlockedEmailAddress extends AbstractDomainEntity
{
    public function __construct(
        #[ORM\Column(length: 255)]
        private string $emailAddress,

        #[ORM\Column(length: 255)]
        private string $blockReason,
        int $id = null
    ) {
        parent::__construct($id);
    }

    public function getEmailAddress(): string
    {
        return $this->emailAddress;
    }

    public function setEmailAddress(string $emailAddress): void
    {
        $this->emailAddress = $emailAddress;
    }

    public function getBlockReason(): string
    {
        return $this->blockReason;
    }

    public function setBlockReason(string $blockReason): void
    {
        $this->blockReason = $blockReason;
    }
}
