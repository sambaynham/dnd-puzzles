<?php

declare(strict_types=1);

namespace App\Tests\Unit\Services\Core\Domain;

use App\Services\Core\Domain\AbstractValueObject;
use App\Services\Core\Domain\Exceptions\InvalidHandleException;
use PHPUnit\Framework\TestCase;

abstract class AbstractValueObjectTestCase extends TestCase
{

    abstract function generateTestValueObject(array $overrides = []): AbstractValueObject;
    public function testInvalidHandle(): void {
        $this->expectException(InvalidHandleException::class);
        $this->generateTestValueObject(['handle' => 'This handle has spaces and &^$% characters in it']);
    }
}
