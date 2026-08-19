<?php

namespace OneToMany\AI\Tests\Resource\Query;

use OneToMany\AI\Exception\InvalidArgumentException;
use OneToMany\AI\Resource\Query\InputText;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

#[Group('UnitTests')]
#[Group('ResourceTests')]
#[Group('QueryTests')]
final class InputTextTest extends TestCase
{
    public function testConstructorRequiresNonEmptyText(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageIs('The input text cannot be empty.');

        new InputText('');
    }
}
