<?php

namespace OneToMany\AI\Bridge;

use OneToMany\AI\Contract\Exception\ExceptionInterface;
use OneToMany\AI\Exception\RuntimeException;
use OneToMany\AI\Provider;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface as HttpResponseInterface;

use function implode;
use function is_array;
use function is_string;
use function sprintf;
use function trim;

readonly class Transport
{
    public function __construct(
        private Provider $provider,
        private HttpClientInterface $httpClient,
        private SerializerInterface $serializer,
    ) {
    }

    /**
     * @param array<string, mixed> $options
     *
     * @throws RuntimeException when sending the request fails
     * @throws RuntimeException when reading the response status fails
     * @throws RuntimeException when an unsuccessful response is returned
     */
    public function request(string $method, string $url, array $options = []): HttpResponseInterface
    {
        try {
            $response = $this->httpClient->request($method, $url, $options);
        } catch (ExceptionInterface $e) {
            throw $e;
        } catch (\Throwable $e) {
            throw new RuntimeException(sprintf('Sending the %s request failed.', $this->provider->getName()), previous: $e);
        }

        $this->assertSuccessful($response);

        return $response;
    }

    /**
     * @template T of object
     *
     * @param class-string<T> $type
     * @param array<string, mixed> $options
     *
     * @return T
     *
     * @throws ExceptionInterface when response payload validation fails
     * @throws RuntimeException when sending the request fails
     * @throws RuntimeException when reading or deserializing the response fails
     * @throws RuntimeException when an unsuccessful response is returned
     */
    public function requestObject(string $method, string $url, string $type, array $options = []): object
    {
        return $this->decode($this->request($method, $url, $options), $type);
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
        } catch (ExceptionInterface $e) {
            throw $e;
        } catch (\Throwable $e) {
            throw new RuntimeException(sprintf('Reading the %s response headers failed.', $this->provider->getName()), previous: $e);
        }
    }

    /**
     * @template T of object
     *
     * @param class-string<T> $type
     *
     * @return T
     *
     * @throws ExceptionInterface when response payload validation fails
     * @throws RuntimeException when reading the response fails
     * @throws RuntimeException when deserializing the response fails
     * @throws RuntimeException when the deserialized response has an unexpected type
     */
    public function decode(HttpResponseInterface $response, string $type): object
    {
        try {
            $content = $response->getContent(false);
        } catch (ExceptionInterface $e) {
            throw $e;
        } catch (\Throwable $e) {
            throw new RuntimeException(sprintf('Reading the %s response failed.', $this->provider->getName()), previous: $e);
        }

        try {
            $payload = $this->serializer->deserialize($content, $type, 'json');
        } catch (ExceptionInterface $e) {
            throw $e;
        } catch (\Throwable $e) {
            throw new RuntimeException(sprintf('Deserializing the %s response failed.', $this->provider->getName()), previous: $e);
        }

        if (!$payload instanceof $type) {
            throw new RuntimeException(sprintf('Deserializing the %s response as "%s" failed.', $this->provider->getName(), $type));
        }

        return $payload;
    }

    public function url(string ...$parts): string
    {
        return implode('/', $parts);
    }

    /**
     * @throws RuntimeException when reading the response status fails
     * @throws RuntimeException when an unsuccessful response is returned
     */
    private function assertSuccessful(HttpResponseInterface $response): void
    {
        try {
            $status = $response->getStatusCode();
        } catch (ExceptionInterface $e) {
            throw $e;
        } catch (\Throwable $e) {
            throw new RuntimeException(sprintf('The %s request failed.', $this->provider->getName()), previous: $e);
        }

        if ($status >= 200 && $status < 300) {
            return;
        }

        throw new RuntimeException($this->errorMessage($response) ?? sprintf('%s returned HTTP %d.', $this->provider->getName(), $status), $status);
    }

    private function errorMessage(HttpResponseInterface $response): ?string
    {
        try {
            $data = $response->toArray(false);
        } catch (\Throwable) {
            return null;
        }

        if (!is_array($error = $data['error'] ?? null)) {
            return null;
        }

        if (!is_string($message = $error['message'] ?? null)) {
            return null;
        }

        $message = trim($message);

        return '' !== $message ? $message : null;
    }
}
