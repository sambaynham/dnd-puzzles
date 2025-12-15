<?php

declare(strict_types=1);

namespace App\Tests\Unit\Services\User\Domain;

use App\Services\User\Domain\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    private static function generateTestUser(array $overrides = []): User {
        return new User(
            email: $overrides['email'] ?? 'test@test.local',
            username: $overrides['username'] ?? 'Test User',
            password: $overrides['password'] ?? 'Test Pass',
            id: $overrides['id'] ?? null
        );
    }
    public function testConstruct(): void {
        $testEmail = 'test@test.local';
        $userName = 'Test User';
        $password = 'TestPass';
        $id = 123;
        $user = new User(
            email: $testEmail,
            username: $userName,
            password: $password,
            id: $id
        );
        self::assertEquals($testEmail, $user->getEmail());
        self::assertEquals($userName, $user->getUsername());
        self::assertEquals($password, $user->getPassword());
        self::assertEquals($id, $user->getId());
        self::assertEmpty($user->getRoles());
        self::assertEmpty($user->getGames());
        self::assertEmpty($user->getGamesMastered());
    }

}
