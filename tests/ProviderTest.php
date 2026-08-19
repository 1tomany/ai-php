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

    public function testCreateRequiresValidProvider(): void
    {
        $provider = 'invalid_provider';

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageIs('The provider "'.$provider.'" is not valid.');

        Provider::create($provider);
    }

    public function testFromModelRequiresValidFormat(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageIs('The model must use the "provider:model" format.');

        Provider::fromModel('gemini');
    }

    #[DataProvider('providerModelAndProvider')]
    public function testFromModel(string $model, Provider $provider): void
    {
        $this->assertSame($provider, Provider::fromModel($model));
    }

    /**
     * @return non-empty-list<array{non-empty-string, Provider}>
     */
    public static function providerModelAndProvider(): array
    {
        $provider = [
            ['gemini:', Provider::Gemini],
            ['openai:', Provider::OpenAI],
            ['gemini:gemini-flash', Provider::Gemini],
            ['openai:gpt-5.6-sol', Provider::OpenAI],
        ];

        return $provider;
    }
}
