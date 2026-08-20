<?php

namespace OneToMany\AI\Tests;

use OneToMany\AI\Exception\InvalidArgumentException;
use OneToMany\AI\Vendor;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

use function array_map;

#[Group('UnitTests')]
final class VendorTest extends TestCase
{
    #[DataProvider('providerVendor')]
    public function testCreateReturnsSelf(Vendor $vendor): void
    {
        $this->assertSame($vendor, Vendor::create($vendor));
    }

    /**
     * @return non-empty-list<array{Vendor}>
     */
    public static function providerVendor(): array
    {
        return array_map(static fn (Vendor $v): array => [$v], Vendor::cases());
    }

    public function testCreateRequiresValidVendor(): void
    {
        $vendor = 'invalid_vendor';

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageIs('The vendor "'.$vendor.'" is not valid.');

        Vendor::create($vendor);
    }

    public function testFromModelRequiresValidFormat(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageIs('The model must use the "vendor:model" format.');

        Vendor::fromModel('gemini');
    }

    #[DataProvider('providerModelAndVendor')]
    public function testFromModel(string $model, Vendor $vendor): void
    {
        $this->assertSame($vendor, Vendor::fromModel($model));
    }

    /**
     * @return non-empty-list<array{non-empty-string, Vendor}>
     */
    public static function providerModelAndVendor(): array
    {
        $provider = [
            ['gemini:', Vendor::Gemini],
            ['openai:', Vendor::OpenAI],
            ['gemini:gemini-flash', Vendor::Gemini],
            ['openai:gpt-5.6-sol', Vendor::OpenAI],
        ];

        return $provider;
    }
}
