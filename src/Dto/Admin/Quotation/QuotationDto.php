<?php

declare(strict_types=1);

namespace App\Dto\Admin\Quotation;

use Symfony\Component\Validator\Constraints as Assert;

class QuotationDto
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Type('string')]
        #[Assert\Length(min: 8, max: 1024)]
        public ? string $quotation = null,

        #[Assert\NotBlank]
        #[Assert\Type('string')]
        #[Assert\Length(min: 8, max: 255)]
        public ? string $citation = null
    ) {}
}
