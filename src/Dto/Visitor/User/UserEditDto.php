<?php

declare(strict_types=1);

namespace App\Dto\Visitor\User;

use App\Entity\User;
use App\Validator as CustomAssert;
use Symfony\Component\Validator\Constraints as Assert;

class UserEditDto
{
    public function __construct(
        #[Assert\Email]
        #[Assert\NotBlank]
        #[CustomAssert\EmailAddressIsNotBlockedConstraint]
        public ? string $emailAddress = null,

        #[Assert\NotBlank]
        #[Assert\Type('string')]
        public ? string $userName = null
    ) {}


    public static function makeFromUser(User $user): static {
        return new static(
            emailAddress: $user->getEmail(),
            userName: $user->getUsername(),
        );
    }
}
