<?php

namespace OneToMany\AI\Resource;

use OneToMany\AI\Contract\Bridge\ProviderInterface;
use OneToMany\AI\Exception\InvalidArgumentException;
use OneToMany\AI\Provider;

use function sprintf;

/**
 * @template T of ProviderInterface
 */
final readonly class ProviderRegistry
{
    /**
     * @var array<non-empty-lowercase-string, T>
     */
    private array $providers;

    /**
     * @param iterable<T> $providers
     *
     * @throws InvalidArgumentException when a provider is already registered
     */
    public function __construct(iterable $providers)
    {
        $indexedProviders = [];

        foreach ($providers as $provider) {
            $key = $provider->provider()->getValue();

            if (isset($indexedProviders[$key])) {
                throw new InvalidArgumentException(sprintf('The "%s" provider is already registered.', $key));
            }

            $indexedProviders[$key] = $provider;
        }

        $this->providers = $indexedProviders;
    }

    /**
     * @return T
     *
     * @throws InvalidArgumentException when a provider is not registered
     */
    public function get(Provider $provider): ProviderInterface
    {
        return $this->providers[$provider->getValue()]
            ?? throw new InvalidArgumentException(sprintf('The "%s" provider is not registered.', $provider->getValue()));
    }
}
