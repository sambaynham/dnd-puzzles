<?php

declare(strict_types=1);

namespace App\Validator;

use App\Repository\BlockedEmailAddressRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class EmailAddressIsNotBlockedConstraintValidator extends ConstraintValidator
{
    public function __construct(private BlockedEmailAddressRepository $blockedEmailAddressRepository) {

    }

    public function validate(mixed $value, Constraint $constraint)
    {
        if (!$constraint instanceof EmailAddressIsNotBlockedConstraint) {
            throw new UnexpectedTypeException($constraint, EmailAddressIsNotBlockedConstraint::class);
        }

        // custom constraints should ignore null and empty values to allow
        // other constraints (NotBlank, NotNull, etc.) to take care of that
        if (null === $value || '' === $value) {
            return;
        }

        $block = $this->blockedEmailAddressRepository->findByEmail($value);
        if ($block !== null) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ emailAddress }}', $value)
                ->setParameter('{{ reason }}', $block->getBlockReason())
                ->addViolation();
        }
        return;

    }
}
