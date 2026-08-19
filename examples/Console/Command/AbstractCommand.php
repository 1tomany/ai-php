<?php

namespace OneToMany\AI\Example\Console\Command;

use OneToMany\AI\Example\Console\AIFactory;
use OneToMany\AI\Example\Console\ApiKeyResolver;
use OneToMany\AI\Provider;

abstract readonly class AbstractCommand
{
    public function __construct(
        protected AIFactory $factory,
        private ApiKeyResolver $apiKeys,
    ) {
    }

    /**
     * @return non-empty-string
     */
    protected function apiKey(
        Provider $provider,
        #[\SensitiveParameter] ?string $apiKey,
    ): string {
        return $this->apiKeys->resolve($provider, $apiKey);
    }
}
