<?php

namespace OneToMany\AI\Tests\Resource\File;

use OneToMany\AI\Exception\InvalidArgumentException;
use OneToMany\AI\Resource\File\RemoteFile;
use OneToMany\AI\Vendor;
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

        new RemoteFile(Vendor::OpenAI, null, null, 'image/jpeg');
    }

    public function testConstructorRequiresUriWhenVendorIsGemini(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageIs('Gemini requires a non-empty URI.');

        new RemoteFile(Vendor::Gemini, uniqid(), null, 'image/jpeg');
    }

    public function testConstructorRequiresNonEmptyMimeType(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageIs('The MIME type cannot be empty.');

        new RemoteFile(Vendor::Gemini, uniqid(), uniqid(), null);
    }
}
