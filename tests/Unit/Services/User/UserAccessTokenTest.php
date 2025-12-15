<?php

declare(strict_types=1);

namespace App\Tests\Unit\Services\User;

use App\Services\User\Domain\User;
use App\Services\User\Domain\UserAccessToken;
use PHPUnit\Framework\TestCase;

class UserAccessTokenTest extends TestCase
{
    public function testMakeForUser(): void {
        $user = $this->createConfiguredMock(User::class, [
            'getUserIdentifier' => 'test@something.com'
        ]);
        $token = UserAccessToken::makeTokenForUser($user);
        self::assertEquals('test@something.com', $token->getUserIdentifier());
        self::assertEquals(64, strlen($token->getToken()));
        self::assertFalse($token->isExpired());
    }
}
