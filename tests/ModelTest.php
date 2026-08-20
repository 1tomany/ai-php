<?php

namespace OneToMany\AI\Tests;

use OneToMany\AI\Exception\InvalidArgumentException;
use OneToMany\AI\Model;
use OneToMany\AI\Vendor;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

#[Group('UnitTests')]
final class ModelTest extends TestCase
{
    public function testConstructorRequiresNonEmptyName(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageIs('The model name cannot be empty.');

        new Model(Vendor::Gemini, '');
    }

    public function testToStringReturnsFormattedName(): void
    {
        $this->assertSame('openai:gpt-5.6-sol', new Model(Vendor::OpenAI, 'gpt-5.6-sol')->__toString());
    }

    public function testCreateReturnsSelf(): void
    {
        $model = new Model(Vendor::OpenAI, 'gpt-5.6-luna');

        $this->assertSame($model, Model::create($model));
    }
}
