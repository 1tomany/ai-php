<?php

namespace OneToMany\AI\Tests\Resource\File;

use OneToMany\AI\Exception\InvalidArgumentException;
use OneToMany\AI\Resource\File\RemoteFile;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

#[Group('UnitTests')]
#[Group('ResourceTests')]
#[Group('FileTests')]
final class RemoteFileTest extends TestCase
{
    public function testConstructorRequiresNonEmptyId(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageIs('The file ID cannot be empty.');

        new RemoteFile(null, null, 'image/jpeg');
    }

    public function testConstructorRequiresMimeTypeToBeNonEmptyWhenNotNull(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageIs('The MIME type cannot be empty.');

        new RemoteFile(uniqid(), uniqid(), ' ');
    }
}
