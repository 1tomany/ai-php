<?php

namespace OneToMany\AI\Resource;

use OneToMany\AI\Contract\Bridge\ProviderInterface;
use OneToMany\AI\Exception\InvalidArgumentException;
use OneToMany\AI\Provider;

use function sprintf;

/**
 * @template T of ProviderInterface
 */
final readonly class Registry
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
            if (isset($indexedProviders[$provider->provider()->getValue()])) {
                throw new InvalidArgumentException(sprintf('The "%s" provider is already registered.', $provider->provider()->getValue()));
            }

            $indexedProviders[$provider->provider()->getValue()] = $provider;
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
        if (!isset($this->providers[$provider->getValue()])) {
            throw new InvalidArgumentException(sprintf('The "%s" provider is not registered.', $provider->getValue()));
        }

        return $this->providers[$provider->getValue()];
    }
}
