<?php

declare(strict_types=1);

namespace App\Services\BlockedEmailAddress\Service;

use App\Services\BlockedEmailAddress\Domain\BlockedEmailAddress;
use App\Services\BlockedEmailAddress\Infrastructure\BlockedEmailAddressRepository;

class BlockedEmailAddressService
{
    public function __construct(private BlockedEmailAddressRepository $blockedEmailAddressRepository)
    {
    }

    public function findByEmail(string $email): ? BlockedEmailAddress {
        return $this->blockedEmailAddressRepository->findByEmail($email);
    }
}
