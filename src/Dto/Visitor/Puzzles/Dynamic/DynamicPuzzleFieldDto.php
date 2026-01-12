<?php

declare(strict_types=1);

namespace App\Dto\Visitor\Puzzles\Dynamic;

class DynamicPuzzleFieldDto
{
    public function __construct(
        private readonly string $name,
        private readonly string $type,
        private mixed $value = null
    ) {}

    public function getName(): string {
        return $this->name;
    }

    public function getType(): string {
        return $this->type;
    }

    public function getValue(): mixed {
        return $this->value;
    }

    public function setValue(mixed $value): void {
        $this->value = $value;
    }

    public function clearValue(): void {
        $this->value = null;
    }

    public function hasValue(): bool {
        return $this->value !== null;
    }

}
