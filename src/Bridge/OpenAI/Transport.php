<?php

namespace OneToMany\AI\Bridge\OpenAI;

use OneToMany\AI\Bridge\Decoded;
use OneToMany\AI\Bridge\OpenAI\Payload\ErrorResponse;
use OneToMany\AI\Bridge\ResponseDecoder;
use OneToMany\AI\Exception\RuntimeException;
use OneToMany\AI\Provider;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface as HttpResponseInterface;

use function implode;
use function sprintf;
use function trim;

final readonly class Transport
{
    public function __construct(
        private HttpClientInterface $httpClient,
        private ResponseDecoder $decoder,
        private string $apiVersion = 'v1',
    ) {
    }

    /**
     * @param array<string, mixed> $options
     */
    public function request(string $method, string $url, array $options = []): HttpResponseInterface
    {
        try {
            $response = $this->httpClient->request($method, $url, $options);
        } catch (\Throwable $e) {
            throw new RuntimeException('Sending the OpenAI request failed.', previous: $e);
        }

        $this->assertSuccessful($response);

        return $response;
    }

    /**
     * @param array<string, mixed> $options
     *
     * @template T of object
     *
     * @param class-string<T> $type
     *
     * @return Decoded<T>
     */
    public function requestObject(string $method, string $url, string $type, array $options = []): Decoded
    {
        return $this->decode($this->request($method, $url, $options), $type);
    }

    /**
     * @template T of object
     *
     * @param class-string<T> $type
     *
     * @return Decoded<T>
     */
    public function decode(HttpResponseInterface $response, string $type): Decoded
    {
        try {
            $content = $response->getContent(false);
        } catch (\Throwable $e) {
            throw new RuntimeException('Reading the OpenAI response failed.', previous: $e);
        }

        return $this->decoder->decode(Provider::OpenAI, $content, $type);
    }

    public function url(string ...$parts): string
    {
        return implode('/', [$this->apiVersion, ...$parts]);
    }

    private function assertSuccessful(HttpResponseInterface $response): void
    {
        try {
            $status = $response->getStatusCode();
        } catch (\Throwable $e) {
            throw new RuntimeException('The OpenAI request failed.', previous: $e);
        }

        if ($status >= 200 && $status < 300) {
            return;
        }

        try {
            $message = $this->decode($response, ErrorResponse::class)->payload->error?->message;
            $message = null !== $message ? trim($message) : '';
            $message = '' !== $message ? $message : null;
        } catch (\Throwable) {
            $message = null;
        }

        throw new RuntimeException($message ?? sprintf('OpenAI returned HTTP %d.', $status), $status);
    }
}
