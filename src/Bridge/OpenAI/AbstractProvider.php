<?php

namespace OneToMany\AI\Bridge\OpenAI;

use OneToMany\AI\Bridge\Transport;
use OneToMany\AI\Contract\Bridge\ProviderInterface;
use OneToMany\AI\Exception\InvalidArgumentException;
use OneToMany\AI\Provider;
use Symfony\Contracts\HttpClient\ResponseInterface as HttpResponseInterface;

use function array_replace;
use function trim;

abstract readonly class AbstractProvider implements ProviderInterface
{
    private const string BASE_URL = 'https://api.openai.com';

    /**
     * @var non-empty-string
     */
    private string $apiKey;

    /**
     * @throws InvalidArgumentException when the API key is empty
     */
    public function __construct(
        protected Transport $transport,
        #[\SensitiveParameter]
        string $apiKey,
        protected string $apiVersion = 'v1',
    ) {
        if ('' === $apiKey = trim($apiKey)) {
            throw new InvalidArgumentException('The OpenAI API key cannot be empty.');
        }

        $this->apiKey = $apiKey;
    }

    /**
     * @see OneToMany\AI\Contract\Bridge\ProviderInterface
     */
    #[\Override]
    public function provider(): Provider
    {
        return Provider::OpenAI;
    }

    /**
     * @param array<string, mixed> $options
     */
    protected function postRequest(string $url, array $options = []): HttpResponseInterface
    {
        return $this->transport->postRequest($url, $this->authenticate($options));
    }

    /**
     * @param array<string, mixed> $options
     */
    protected function deleteRequest(string $url, array $options = []): HttpResponseInterface
    {
        return $this->transport->deleteRequest($url, $this->authenticate($options));
    }

    protected function url(string ...$parts): string
    {
        return $this->transport->url(self::BASE_URL, ...$parts);
    }

    /**
     * @param array<string, mixed> $options
     *
     * @return array<string, mixed>
     */
    private function authenticate(array $options): array
    {
        return array_replace($options, [
            'auth_bearer' => $this->apiKey,
        ]);
    }
}
