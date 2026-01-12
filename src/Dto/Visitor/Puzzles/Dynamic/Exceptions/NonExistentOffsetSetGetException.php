<?php

declare(strict_types=1);

namespace App\Dto\Visitor\Puzzles\Dynamic\Exceptions;

class NonExistentOffsetSetGetException extends \Exception
{
    public function __construct(bool|float|int|string|null $offset, string $mode, int $code = 0, ?\Throwable $previous = null) {

        parent::__construct(sprintf("Trying to %s non-existent offset '%s'", $mode, $offset), $code, $previous);
    }
}
