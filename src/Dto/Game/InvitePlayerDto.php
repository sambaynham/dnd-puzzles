<?php

declare(strict_types=1);

namespace App\Dto\Game;

use App\Entity\Game;
use App\Validator as CustomAssert;
use Symfony\Component\Validator\Constraints as Assert;

class InvitePlayerDto
{
    #[Assert\Type('string')]
    #[Assert\NotBlank()]
    public ? string $invitationCode = null;

    #[Assert\Type('string')]
    #[Assert\NotBlank()]
    #[Assert\Length(min: 1, max: 255)]
    public ? string $invitationText = null;

    #[Assert\Email()]
    #[Assert\NotBlank()]
    #[CustomAssert\EmailAddressIsNotBlockedConstraint]
    public ?string $email = null;

    public ? Game $game = null;

}
