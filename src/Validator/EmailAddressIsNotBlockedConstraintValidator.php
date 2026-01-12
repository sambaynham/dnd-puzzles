<?php

declare(strict_types=1);

namespace App\Validator;


use App\Services\BlockedEmailAddress\Service\BlockedEmailAddressService;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class EmailAddressIsNotBlockedConstraintValidator extends ConstraintValidator
{
    public function __construct(private readonly BlockedEmailAddressService $blockedEmailAddressService) {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof EmailAddressIsNotBlockedConstraint) {
            throw new UnexpectedTypeException($constraint, EmailAddressIsNotBlockedConstraint::class);
        }

        if (null !== $value && !is_string($value)) {
            throw new UnexpectedTypeException($value, 'string');
        }

        // custom constraints should ignore null and empty values to allow
        // other constraints (NotBlank, NotNull, etc.) to take care of that
        if (null === $value || '' === $value) {
            return;
        }

        $block = $this->blockedEmailAddressService->findByEmail($value);
        if ($block !== null) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ emailAddress }}', $value)
                ->setParameter('{{ reason }}', $block->getBlockReason())
                ->addViolation();
        }
    }
}
