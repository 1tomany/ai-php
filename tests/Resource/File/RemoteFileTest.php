<?php

namespace OneToMany\AI\Tests\Resource\File;

use OneToMany\AI\Exception\InvalidArgumentException;
use OneToMany\AI\Provider;
use OneToMany\AI\Resource\File\RemoteFile;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

use function uniqid;

#[Group('UnitTests')]
#[Group('ResourceTests')]
#[Group('FileTests')]
final class RemoteFileTest extends TestCase
{
    public function testConstructorRequiresNonEmptyId(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageIs('The file ID cannot be empty.');

        new RemoteFile(Provider::OpenAI, '', 'text/plain');
    }

    public function testConstructorRequiresNonEmptyMediaType(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageIs('The media type cannot be empty.');

        new RemoteFile(Provider::OpenAI, uniqid(), '');
    }
}
