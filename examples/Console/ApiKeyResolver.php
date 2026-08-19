<?php

namespace OneToMany\AI\Example\Console;

use OneToMany\AI\Exception\RuntimeException;
use OneToMany\AI\Provider;

use function getenv;
use function is_string;
use function sprintf;
use function strtoupper;
use function trim;

final readonly class ApiKeyResolver
{
    /**
     * @return non-empty-string
     *
     * @throws RuntimeException when no API key is provided or defined in the environment
     */
    public function resolve(
        Provider $provider,
        #[\SensitiveParameter] ?string $apiKey,
    ): string {
        $environmentVariable = sprintf('%s_API_KEY', strtoupper($provider->getValue()));

        if (null === $apiKey) {
            $apiKey = $_SERVER[$environmentVariable]
                ?? $_ENV[$environmentVariable]
                ?? getenv($environmentVariable);
        }

        if (!is_string($apiKey) || '' === $apiKey = trim($apiKey)) {
            throw new RuntimeException(sprintf('No API key was found for the "%s" provider. Provide the "--api-key" option or define the "%s" environment variable.', $provider->getValue(), $environmentVariable));
        }

        return $apiKey;
    }
}
