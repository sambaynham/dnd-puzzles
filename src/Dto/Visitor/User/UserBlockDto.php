<?php

declare(strict_types=1);

namespace App\Dto\Visitor\User;

use App\Services\User\Domain\User;
use Symfony\Component\Validator\Constraints as Assert;

class UserBlockDto
{
    public function __construct(
        public readonly User $user,
        #[Assert\NotBlank]
        public bool $confirm = false,
        #[Assert\NotBlank]
        #[Assert\Length(min: 1, max: 255)]
        public ? string $reason = null,
        public ? \DateTimeInterface $expiresAt = null
    ) {

    }
}
