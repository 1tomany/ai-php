<?php

namespace OneToMany\AI\Bridge;

use OneToMany\AI\Bridge\Common\Response\Error\GenericError;
use OneToMany\AI\Exception\RuntimeException;
use Symfony\Component\Serializer\Exception\ExceptionInterface as SerializerExceptionInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\UnwrappingDenormalizer;
use Symfony\Contracts\HttpClient\Exception\ExceptionInterface as HttpClientExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface as HttpResponseInterface;

use function array_first;
use function array_is_list;
use function implode;
use function is_array;

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
    public function assertSuccessful(HttpResponseInterface $response): void
    {
        try {
            $statusCode = $response->getStatusCode();
        } catch (HttpClientExceptionInterface $e) {
            throw new RuntimeException('The request failed.', previous: $e);
        }

        if ($statusCode < 200 || $statusCode >= 300) {
            if (null === $message = $this->extractErrorMessage($response)) {
                $message = sprintf('[HTTP %d] The request was unsuccessful.', $statusCode);
            }

            throw new RuntimeException($message, $statusCode);
        }
    }

    /**
     * @return ?non-empty-string
     */
    private function extractErrorMessage(HttpResponseInterface $response): ?string
    {
        try {
            $data = $response->toArray(false);
        } catch (HttpClientExceptionInterface) {
            return null;
        }

        if (array_is_list($data)) {
            $data = array_first($data);
        }

        if (!is_array($data)) {
            return null;
        }

        try {
            $error = $this->denormalizer->denormalize($data, GenericError::class, null, [
                UnwrappingDenormalizer::UNWRAP_PATH => '[error]',
            ]);
        } catch (SerializerExceptionInterface) {
            return null;
        }

        return $error->message ?? $error->code;
    }
}
