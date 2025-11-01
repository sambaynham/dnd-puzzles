<?php

declare(strict_types=1);

namespace App\Dto\Visitor\Game\Invitations;

use App\Entity\GameInvitation;
use Symfony\Component\Validator\Constraints as Assert;

class DeclineInvitationDto
{
    public function __construct(

        public GameInvitation $invitation,

        #[Assert\NotBlank]
        public ? string $reason = null,

        #[Assert\NotBlank]
        public ?string $notes = null,
    ) {}
}
