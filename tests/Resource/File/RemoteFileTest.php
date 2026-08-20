<?php

namespace OneToMany\AI\Tests\Resource\File;

use OneToMany\AI\Exception\InvalidArgumentException;
use OneToMany\AI\Provider;
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

        new RemoteFile(Provider::OpenAI, null, null, 'image/jpeg');
    }

    public function testConstructorRequiresUriWhenProviderIsGemini(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageIs('Gemini requires a non-empty URI.');

        new RemoteFile(Provider::Gemini, uniqid(), null, 'image/jpeg');
    }

    public function testConstructorRequiresNonEmptyMimeType(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageIs('The MIME type cannot be empty.');

        new RemoteFile(Provider::Gemini, uniqid(), uniqid(), null);
    }
}
