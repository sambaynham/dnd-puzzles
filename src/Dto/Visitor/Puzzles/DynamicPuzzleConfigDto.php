<?php

declare(strict_types=1);

namespace App\Dto\Visitor\Puzzles;

use App\Services\Puzzle\Domain\PuzzleTemplate;
use App\Services\Puzzle\Domain\ConfigOptionDefinition;
use App\Validator as CustomAssert;
class DynamicPuzzleConfigDto implements \ArrayAccess

{

    private function __construct(
        #[CustomAssert\PuzzleTemplateFieldsConstraint]
        private array $fields
    ) {
    }

    public function __isset(string $name) : bool {
        return $this->fields[$name]['value'] !== null;
    }

    public function __get(string $name): mixed {
        return $this->fields[$name]['value'];
    }

    public function __set(string $name, mixed $value): void {
        $this->fields[$name]['value'] = $value;
    }

    public static function makeFromTemplateDefinition(PuzzleTemplate $template): DynamicPuzzleConfigDto {

        return new static(self::mapConfiguration($template->getConfiguration()));
    }

    /**
     * @param array $configuration<ConfigOptionDefinition>
     */
    private static function mapConfiguration(array $configuration): array {
        $fields = [];
        foreach ($configuration as $configOption) {
            $fields[$configOption->getConfigName()] = [
                'name' => $configOption->getConfigName(),
                'type' => $configOption->getType(),
                'value' =>  null
            ];
        }
        return $fields;

    }

    public function offsetExists(mixed $offset): bool
    {
         return isset($this->fields[$offset]) && $this->fields[$offset]['value'] ?? null !== null;
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->fields[$offset]['value'] ?? null;
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->fields[$offset]['value'] = $value;
    }

    public function offsetUnset(mixed $offset): void
    {
        $this->fields[$offset]['value'] = null;
    }
}
