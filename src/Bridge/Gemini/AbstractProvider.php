<?php

namespace OneToMany\AI\Bridge\Gemini;

use OneToMany\AI\Bridge\Transport;
use OneToMany\AI\Contract\Bridge\ProviderInterface;
use OneToMany\AI\Exception\InvalidArgumentException;
use OneToMany\AI\Provider;

use function sprintf;
use function trim;

abstract readonly class AbstractProvider implements ProviderInterface
{
    /**
     * @var non-empty-string
     */
    protected string $apiKey;

    public const string BASE_URL = 'https://generativelanguage.googleapis.com';

    /**
     * @throws InvalidArgumentException when the API key is empty
     */
    public function __construct(
        protected Transport $transport,
        #[\SensitiveParameter] string $apiKey,
        protected string $apiVersion = 'v1beta',
    ) {
        if ('' === $apiKey = trim($apiKey)) {
            throw new InvalidArgumentException(sprintf('The %d API key cannot be empty.', $this->provider()->getName()));
        }

        $this->apiKey = $apiKey;
    }

    /**
     * @see OneToMany\AI\Contract\Bridge\ProviderInterface
     */
    #[\Override]
    public function provider(): Provider
    {
        return Provider::Gemini;
    }

    protected function url(string ...$parts): string
    {
        return $this->transport->url(self::BASE_URL, ...$parts);
    }
}
