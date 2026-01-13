<?php

namespace App\Services\User\Domain\Interfaces;

use App\Services\User\Domain\User;

interface UserAccessTokenInterface
{
    public function __construct(
        string $userIdentifier,
        string $token,
        \DateTimeImmutable $expiresAt,
        ?int $id = null
    );

    public static function makeTokenForUser(User $user): static;

    /**
     * @return non-empty-string
     */
    public function getUserIdentifier(): string;

    /**
     * @return non-empty-string
     */
    public function getToken(): string;

    public function isExpired(): bool;
}
