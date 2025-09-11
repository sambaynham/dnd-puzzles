<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\ApiResource\DiceRoll;
use App\State\Exceptions\UnprocessableRollStateException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class DiceStateProvider implements ProviderInterface
{

    private const string CLAUSE_PATTERN = '/^(?:100|[1-9][0-9]?)d(?:100|[1-9][0-9]?)(?:p(?:100|[1-9][0-9]?))?$/';

    private function processRolls(string $rollState): array {
        $clauses = explode(',', $rollState);
        $rolls = [];
        foreach ($clauses as $clause) {
            $rolls[] = $this->generateRoll($clause);
        }
        return $rolls;
    }

    private function validateRollState(string $rollState): void {
        $clauses = explode(',', $rollState);
        foreach ($clauses as $clause) {
            if (!preg_match(self::CLAUSE_PATTERN, $clause)) {
                throw new UnprocessableRollStateException("Invalid string: must match 'xdy' or 'xdypz' where x,y and z are 1–100.");
            }
        }
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        if (!$uriVariables['rollState']) {
            throw new UnprocessableEntityHttpException("Could not interpret roll state.");
        }
        $decodedRollState = urldecode($uriVariables['rollState']);
        try {
            $this->validateRollState($decodedRollState);
        } catch (UnprocessableRollStateException $e) {
            throw new UnprocessableEntityHttpException($e->getMessage(), $e);
        }
        return $this->processRolls($decodedRollState);
    }

    private function generateRoll(string $rollClause): DiceRoll {
        $bonus = 0;
        if (str_contains(needle: 'p', haystack: $rollClause)) {
            $components = explode('p', $rollClause);
            $bonus = end($components);
            $components = explode('d', reset($components));
            $numDice = $components[0];
            $dieSides = $components[1];
        } else {
            $components = explode('d', $rollClause);
            $numDice = $components[0];
            $dieSides = $components[1];
        }
        $rolled = self::rollDice($numDice, $dieSides);
        return new DiceRoll(id: $rollClause, dieSides: $dieSides, roll: $rolled, total: $rolled+$bonus, bonus: $bonus);
    }

    public static function rollDice(int $numDice, int $dieSides): int {
        $total = 0;
        for ($i=1; $i<=$numDice; $i++) {
            $total+= rand(min: 1, max: $dieSides);
        }
        return $total;
    }
}
