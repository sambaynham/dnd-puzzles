<?php

namespace App\Dto\User;

use App\Validator as CustomAssert;
use Symfony\Component\Validator\Constraints as Assert;

class UserDto
{
    #[Assert\Email]
    #[Assert\NotBlank]
    #[CustomAssert\EmailAddressIsNotBlockedConstraint]
    public ? string $emailAddress = null;


    #[Assert\NotBlank]
    #[Assert\Type('string')]
    public ? string $userName = null;

    #[Assert\NotBlank]
    #[Assert\Type('string')]
    #[Assert\Length(min: 8, max: 255)]
    public ? string $plainPassword = null;
}
