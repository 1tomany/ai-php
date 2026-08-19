<?php

namespace OneToMany\AI\Tests\Resource\File;

use OneToMany\AI\Exception\InvalidArgumentException;
use OneToMany\AI\Resource\File\LocalFile;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

#[Group('UnitTests')]
#[Group('ResourceTests')]
#[Group('FileTests')]
final class LocalFileTest extends TestCase
{
    public function testConstructorRequiresNonEmptyPath(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageIs('The file path cannot be empty.');

        new LocalFile('', 'text/plain');
    }

    public function testConstructorRequiresReadableFile(): void
    {
        $path = '/invalid/instructions.txt';
        $this->assertFileDoesNotExist($path);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageIs('The file "'.$path.'" is not readable.');

        new LocalFile($path, 'text/plain');
    }

    public function testConstructorRequiresNonEmptyMediaType(): void
    {
        $path = __FILE__;
        $this->assertFileExists($path);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageIs('The media type cannot be empty.');

        new LocalFile($path, '');
    }
}
