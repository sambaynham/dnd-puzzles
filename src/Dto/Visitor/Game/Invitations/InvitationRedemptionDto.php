<?php

declare(strict_types=1);

namespace App\Dto\Visitor\Game\Invitations;

use App\Validator as CustomAssert;
use Symfony\Component\Validator\Constraints as Assert;

class InvitationRedemptionDto
{
    #[Assert\Email()]
    #[Assert\NotBlank()]
    #[CustomAssert\EmailAddressIsNotBlockedConstraint]
    public ? string $emailAddress = null;

    #[Assert\NotBlank()]
    public ? string $invitationCode = null;
}
