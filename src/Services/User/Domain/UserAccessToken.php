<?php

declare(strict_types=1);

namespace App\Services\User\Domain;

use App\Services\Core\Domain\AbstractDomainEntity;
use App\Services\User\Domain\Interfaces\UserAccessTokenInterface;
use App\Services\User\Infrastructure\Repository\UserAccessTokenRepository;
use Doctrine\ORM\Mapping as ORM;
use Random\RandomException;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(
    repositoryClass: UserAccessTokenRepository::class
)]
#[UniqueEntity("email")]
class UserAccessToken extends AbstractDomainEntity implements UserAccessTokenInterface
{
    private const int TOKEN_TTL_SECONDS =  86400;

    private const int TOKEN_STRING_LENGTH = 128;


    /**
     * @param non-empty-string $userIdentifier
     * @param non-empty-string $token
     * @param \DateTimeImmutable $expiresAt
     * @param int|null $id
     */
    public function __construct(
        #[ORM\Column(type: 'non_empty_string', length: 1024)]
        private readonly string $userIdentifier,
        #[ORM\Column(type: 'non_empty_string', length: 128, unique: true)]
        private readonly string $token,
        #[ORM\Column(type: 'datetime_immutable')]
        private readonly \DateTimeImmutable $expiresAt,

        ?int $id = null
    ) {
        parent::__construct(id: $id);
    }

    /**
     * @throws \DateMalformedIntervalStringException
     * @throws RandomException
     * @throws \DateMalformedStringException
     */
    public static function makeTokenForUser(User $user): static {
        $expiresAt = new \DateTime();
        $expiresAt->modify('+' . self::TOKEN_TTL_SECONDS . ' seconds');

        return new static(
            userIdentifier: $user->getUserIdentifier(),
            token: self::generateAccessToken(),
            expiresAt: \DateTimeImmutable::createFromMutable($expiresAt)
        );
    }

    /**
     * @return non-empty-string
     */
    public function getUserIdentifier(): string {
        return $this->userIdentifier;
    }

    /**
     * @return non-empty-string
     */
    public function getToken(): string {
        return $this->token;
    }

    public function isExpired(): bool {
        $now = new \DateTimeImmutable();
        return $now >= $this->expiresAt;
    }

    /**
     * @throws RandomException
     * @return non-empty-string
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
}
