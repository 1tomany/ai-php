<?php

namespace OneToMany\AI\Tests\Validator;

use OneToMany\AI\Validator\Model;
use OneToMany\AI\Validator\ModelValidator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

final class ModelValidatorTest extends TestCase
{
    public function testValidateRequiresModelConstraint(): void
    {
        $this->expectException(UnexpectedTypeException::class);
        $this->expectExceptionMessageIs('Expected argument of type "'.Model::class.'", "'.Assert\Blank::class.'" given');

        new ModelValidator()->validate('mock:model', new Assert\Blank());
    }

    public function testValidateIgnoresNullValues(): void
    {
        $this->expectNotToPerformAssertions();

        new ModelValidator()->validate(null, new Model());
    }

    public function testValidateRequiresValueToBeString(): void
    {
        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessageIs('Expected argument of type "string", "array" given');

        new ModelValidator()->validate(['mock:model'], new Model());
    }

    public function testValidatingValidModel(): void
    {
        $this->expectNotToPerformAssertions();

        new ModelValidator()->validate('openai:gpt-5.6-sol', new Model());
    }
}
