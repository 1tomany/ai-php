<?php

namespace OneToMany\AI\Tests\Resource\Query;

use OneToMany\AI\Exception\InvalidArgumentException;
use OneToMany\AI\Exception\RuntimeException;
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

    public function testToStringReturnsText(): void
    {
        $faker = \Faker\Factory::create();

        $text = $faker->sentence();
        $this->assertNotEmpty($text);

        $inputText = new InputText($text);
        $this->assertSame($text, $inputText->__toString());
    }

    public function testFromFileRequiresReadingFile(): void
    {
        $path = '/invalid/instructions.txt';
        $this->assertFileDoesNotExist($path);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessageIs('Reading the input text file "'.$path.'" failed.');

        InputText::fromFile($path);
    }
}
