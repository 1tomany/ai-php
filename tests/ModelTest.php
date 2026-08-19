<?php

namespace OneToMany\AI\Tests;

use OneToMany\AI\Exception\InvalidArgumentException;
use OneToMany\AI\Model;
use OneToMany\AI\Provider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

#[Group('UnitTests')]
final class ModelTest extends TestCase
{
    public function testConstructorRequiresNonEmptyName(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageIs('The model name cannot be empty.');

        new Model(Provider::Gemini, '');
    }

    public function testToStringReturnsFormattedName(): void
    {
        $this->assertSame('openai:gpt-5.6-sol', new Model(Provider::OpenAI, 'gpt-5.6-sol')->__toString());
    }
}
