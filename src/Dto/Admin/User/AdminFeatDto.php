<?php

declare(strict_types=1);

namespace App\Dto\Admin\User;

use App\Services\User\Domain\User;
use App\Services\User\Domain\UserFeat;
use App\Services\User\Domain\ValueObjects\Rarity;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;
class AdminFeatDto
{
    private const string SLUG_REGEX = '/^[a-z0-9_]+$/';
    private const string ICON_CLASS_REGEX = '/^[a-z0-9-]+$/';

    /**
     * @param string|null $label
     * @param string|null $handle
     * @param string|null $description
     * @param Collection<int, User>|null $users
     * @param Rarity|null $rarity
     * @param string|null $iconClass
     * @param bool $gamesMasterAwardable
     */
    final public function __construct(
        #[Assert\NotBlank()]
        #[Assert\Type('string')]
        #[Assert\Length(min: 4, max: 255)]
        public ? string $label = null,

        #[Assert\NotBlank()]
        #[Assert\Type('string')]
        #[Assert\Length(min: 4, max: 255)]
        #[Assert\Regex(self::SLUG_REGEX, message: 'The slug must only contain lowercase letters, numbers, and underscores.')]
        public ?string $handle = null,

        #[Assert\NotBlank()]
        #[Assert\Type('string')]
        #[Assert\Length(min: 8, max: 512)]
        public ?string $description = null,

        public ? Collection $users = null,

        #[Assert\NotBlank()]
        public ? Rarity $rarity = null,

        #[Assert\NotBlank()]
        #[Assert\Type('string')]
        #[Assert\Length(min: 1, max: 128)]
        #[Assert\Regex(self::ICON_CLASS_REGEX, message: 'The icon class must only contain lowercase letters, numbers, and hyphens.')]
        public ?string $iconClass = null,

        #[Assert\Type('boolean')]
        public bool $gamesMasterAwardable = false
    ) {
    }

    /**
     * @param UserFeat $feat
     * @return static
     */
    public static function  makeFromFeat(UserFeat $feat): static {
        return new static(
            label: $feat->getLabel(),
            handle: $feat->getHandle(),
            description: $feat->getDescription(),
            rarity: $feat->getRarity(),
            iconClass: $feat->getIconClass(),
            gamesMasterAwardable: $feat->isGamesMasterAwardable(),
        );
    }
}
