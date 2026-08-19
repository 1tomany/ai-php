<?php

namespace OneToMany\AI\Bridge;

use OneToMany\AI\Exception\RuntimeException;
use Symfony\Component\Serializer\Exception\ExceptionInterface as SerializerExceptionInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Contracts\HttpClient\Exception\ExceptionInterface as HttpClientExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface as HttpResponseInterface;

use function implode;
use function is_array;
use function is_string;
use function trim;

readonly class Transport
{
    public function __construct(
        private HttpClientInterface $httpClient,
        private DenormalizerInterface $denormalizer,
    ) {
    }

    /**
     * @see OneToMany\AI\Bridge\Transport::assertSuccessful()
     *
     * @param array<string, mixed> $options
     *
     * @throws RuntimeException when sending the request fails
     */
    public function request(string $method, string $url, array $options = []): HttpResponseInterface
    {
        try {
            $response = $this->httpClient->request($method, $url, $options);
        } catch (HttpClientExceptionInterface $e) {
            throw new RuntimeException('Sending the request failed.', previous: $e);
        }

        $this->assertSuccessful($response);

        return $response;
    }

    /**
     * @see OneToMany\AI\Bridge\Transport::request()
     *
     * @param array<string, mixed> $options
     */
    public function postRequest(string $url, array $options = []): HttpResponseInterface
    {
        return $this->request('POST', $url, $options);
    }

    /**
     * @see OneToMany\AI\Bridge\Transport::request()
     *
     * @param array<string, mixed> $options
     */
    public function deleteRequest(string $url, array $options = []): HttpResponseInterface
    {
        return $this->request('DELETE', $url, $options);
    }

    /**
     * @return array<string, list<string>>
     *
     * @throws RuntimeException when reading the response headers fails
     */
    public function headers(HttpResponseInterface $response): array
    {
        try {
            return $response->getHeaders(false);
        } catch (HttpClientExceptionInterface $e) {
            throw new RuntimeException('Reading the response headers failed.', previous: $e);
        }
    }

    /**
     * @template T of object
     *
     * @param class-string<T> $type
     * @param array<string, mixed> $context
     *
     * @return T
     *
     * @throws RuntimeException when decoding the response fails
     * @throws RuntimeException when denormalizing the response fails
     */
    public function decode(
        HttpResponseInterface $response,
        string $type,
        array $context = [],
    ): object {
        try {
            $data = $response->toArray(false);
        } catch (HttpClientExceptionInterface $e) {
            throw new RuntimeException('Decoding the response failed.', previous: $e);
        }

        try {
            $payload = $this->denormalizer->denormalize($data, $type, 'json', $context);
        } catch (SerializerExceptionInterface $e) {
            throw new RuntimeException('Denormalizing the response failed.', previous: $e);
        }

        return $payload;
    }

    public function url(string ...$parts): string
    {
        return implode('/', $parts);
    }

    /**
     * @throws RuntimeException when the request fails due to a transport error
     * @throws RuntimeException when the request was unsuccessful due to an HTTP error
     */
    private function assertSuccessful(HttpResponseInterface $response): void
    {
        try {
            $status = $response->getStatusCode();
        } catch (HttpClientExceptionInterface $e) {
            throw new RuntimeException('The request failed.', previous: $e);
        }

        if ($status < 200 || $status >= 300) {
            if (null === $error = $this->extractError($response)) {
                $error = sprintf('');
            }

            throw new RuntimeException($this->extractError($response) ?? 'The request was unsuccessful.', $status);
        }
    }

    /**
     * @return ?non-empty-string
     */
    private function extractError(HttpResponseInterface $response): ?string
    {
        $error = null;

        try {
            $data = $response->toArray(false);

            if (isset($data['error'])) {
                $error = $data['error'];

                if (is_array($error)) {
                    if (isset($error['message'])) {
                        if (is_string($error['message'])) {
                            $error = trim($error['message']);
                        }
                    }
                }
            }

            if (null === $error || '' === $error) {
                $error = $response->getInfo('error');
            }

            $error = is_string($error) ? $error : null;
        } catch (HttpClientExceptionInterface) {
        }

        return '' !== $error ? $error : null;
    }
}
