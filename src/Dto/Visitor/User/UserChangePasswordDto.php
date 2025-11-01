<?php

declare(strict_types=1);

namespace App\Dto\Visitor\User;

use Symfony\Component\Security\Core\Validator\Constraints as SecurityAssert;
use Symfony\Component\Validator\Constraints as Assert;

class UserChangePasswordDto
{
    public function __construct(
        #[SecurityAssert\UserPassword(
            message: 'Wrong value for your current password',
        )]
        public ? string $currentPassword = null,

        #[Assert\NotBlank]
        #[Assert\Type('string')]
        public ?string $newPassword = null
    ) {}
}
