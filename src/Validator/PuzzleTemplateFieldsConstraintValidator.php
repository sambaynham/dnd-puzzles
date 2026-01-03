<?php

declare(strict_types=1);

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class PuzzleTemplateFieldsConstraintValidator extends ConstraintValidator
{

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof PuzzleTemplateFieldsConstraint) {
            throw new UnexpectedTypeException($constraint, PuzzleTemplateFieldsConstraint::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        dd($value);
    }
}
