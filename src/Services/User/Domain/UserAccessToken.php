<?php

declare(strict_types=1);

namespace App\Services\User\Domain;

use App\Services\User\Infrastructure\UserAccessTokenRepository;
use Random\RandomException;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(
    repositoryClass: UserAccessTokenRepository::class
)]
#[UniqueEntity("email")]
readonly class UserAccessToken
{
    private const int TOKEN_TTL_SECONDS =  86400;

    private const int TOKEN_STRING_LENGTH = 128;


    private function __construct(
        #[ORM\Column(type: 'string', length: 1024)]
        private string $userIdentifier,
        #[ORM\Column(type: 'string', length: 128, unique: true)]
        private string $token,
        #[ORM\Column(type: 'datetime_immutable')]
        private \DateTimeInterface $expiresAt,
        #[ORM\Id]
        #[ORM\GeneratedValue]
        #[ORM\Column]
        private ?int $id = null
    ) {
    }

    /**
     * @throws \DateMalformedIntervalStringException
     * @throws RandomException
     */
    public static function makeTokenForUser(User $user): static {
        $expiresAt = new \DateTime();
        $expiresAt->add(new \DateInterval(sprintf('PT%d', self::TOKEN_TTL_SECONDS)));

        return new static(
            userIdentifier: $user->getUserIdentifier(),
            token: self::generateAccessToken(),
            expiresAt: $expiresAt
        );
    }

    public function getUserIdentifier(): string {
        return $this->userIdentifier;
    }

    public function getToken(): string {
        return $this->token;
    }

    public function isExpired(): bool {
        $now = new \DateTimeImmutable();
        return $now >= $this->expiresAt;
    }

    /**
     * @throws RandomException
     */
    private static function generateAccessToken(): string {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';

        for ($i = 0; $i < self::TOKEN_STRING_LENGTH; $i++) {
            $randomString .= $characters[random_int(0, $charactersLength - 1)];
        }

        return $randomString;
    }

    public function getId(): ? int {
        return $this->id;
    }
}
