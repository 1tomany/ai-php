<?php

namespace OneToMany\AI\Example\Console\Command;

use OneToMany\AI\Example\Console\AIFactory;
use OneToMany\AI\Example\Console\ApiKeyResolver;
use OneToMany\AI\Provider;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;

use function is_string;

abstract class AbstractAICommand extends Command
{
    public function __construct(
        protected readonly AIFactory $factory,
        private readonly ApiKeyResolver $apiKeys,
    ) {
        parent::__construct();
    }

    protected function addApiKeyOption(): void
    {
        $this->addOption(
            'api-key',
            null,
            InputOption::VALUE_REQUIRED,
            'The provider API key. Defaults to the provider-specific environment variable.',
        );
    }

    /**
     * @return non-empty-string
     */
    protected function apiKey(InputInterface $input, Provider $provider): string
    {
        $apiKey = $input->getOption('api-key');

        return $this->apiKeys->resolve(
            $provider,
            is_string($apiKey) ? $apiKey : null,
        );
    }
}
