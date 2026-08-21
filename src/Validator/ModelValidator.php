<?php

namespace OneToMany\AI\Validator;

use OneToMany\AI\Contract\Exception\ExceptionInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

use function is_string;

final class ModelValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof Model) {
            throw new UnexpectedTypeException($constraint, Model::class);
        }

        if (null === $value) {
            return;
        }

        if (!is_string($value)) {
            throw new UnexpectedValueException($value, 'string');
        }

        try {
            \OneToMany\AI\Model::create($value);
        } catch (ExceptionInterface $e) {
            $this->context->buildViolation($e->getMessage())->addViolation();
        }
    }
}
