<?php

namespace App\Dto\Admin\User;

use App\Entity\User;
use App\Validator as CustomAssert;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;

class AdminUserDto
{
    public function __construct(
        #[Assert\Email]
        #[Assert\NotBlank]
        #[CustomAssert\EmailAddressIsNotBlockedConstraint]
        public ? string $email = null,

        #[Assert\NotBlank]
        #[Assert\Type('string')]
        public ? string $username = null,

        public ? Collection $roles = null,

        #[Assert\Type('string')]
        #[Assert\Length(min: 8, max: 255)]
        public ? string $plainPassword = null
    ) {

    }

    public static function makeFromUser(User $user): self {
        return new static(
            email: $user->getEmail(),
            username: $user->getUsername(),
            roles: $user->getHydratedRoles(),
        );
    }


}
