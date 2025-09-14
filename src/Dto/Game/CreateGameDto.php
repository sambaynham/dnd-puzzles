<?php

declare(strict_types=1);

namespace App\Dto\Game;

use Symfony\Component\Validator\Constraints as Assert;

class CreateGameDto
{

    private const string SLUG_REGEX = '/^[a-z0-9_]+$/';
    #[Assert\NotBlank]
    #[Assert\Length(min: 5, max: 255)]
    public ? string $name = null;

    #[Assert\NotBlank]
    #[Assert\Length(min: 10, max: 1024)]
    public ? string $description = null;

    #[Assert\NotBlank]
    #[Assert\Length(min: 6, max: 128)]
    #[Assert\Regex(self::SLUG_REGEX, message: 'The slug must only contain lowercase letters, numbers, and underscores.')]
    public ? string $slug;
}
