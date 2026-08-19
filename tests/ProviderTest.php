<?php

namespace OneToMany\AI\Tests;

use OneToMany\AI\Exception\InvalidArgumentException;
use OneToMany\AI\Provider;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

use function array_map;

#[Group('UnitTests')]
final class ProviderTest extends TestCase
{
    public function testCreateRequiresValidProvider(): void
    {
        $provider = 'invalid_provider';

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageIs('The provider "'.$provider.'" is not valid.');

        Provider::create($provider);
    }

    #[DataProvider('providerProvider')]
    public function testCreateReturnsSelf(Provider $provider): void
    {
        $this->assertSame($provider, Provider::create($provider));
    }

    /**
     * @return non-empty-list<array{Provider}>
     */
    public static function providerProvider(): array
    {
        return array_map(static fn (Provider $p): array => [$p], Provider::cases());
    }
}
