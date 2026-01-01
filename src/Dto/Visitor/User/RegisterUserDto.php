<?php

namespace App\Dto\Visitor\User;

use App\Validator as CustomAssert;
use Symfony\Component\Validator\Constraints as Assert;

class RegisterUserDto
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

    #[Assert\Type('string')]
    public ? string $invitationCode = null;

    public bool $profilePublic = false;

    public bool $acceptCookies = false;
}
