<?php

namespace OneToMany\AI\Bridge;

use OneToMany\AI\Contract\Exception\ExceptionInterface;
use OneToMany\AI\Exception\RuntimeException;
use OneToMany\AI\Provider;
use Symfony\Component\Serializer\Encoder\DecoderInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

use function is_array;
use function is_string;
use function sprintf;

final readonly class ResponseDecoder
{
    public function __construct(
        private DecoderInterface $decoder,
        private DenormalizerInterface $denormalizer,
    ) {
    }

    /**
     * @template T of object
     *
     * @param class-string<T> $type
     *
     * @return Decoded<T>
     *
     * @throws ExceptionInterface when response payload validation fails
     * @throws RuntimeException when the response is not a JSON object
     * @throws RuntimeException when decoding or denormalizing the response fails
     * @throws RuntimeException when the decoded response has an unexpected type
     */
    public function decode(Provider $provider, string $content, string $type): Decoded
    {
        try {
            $data = $this->decoder->decode($content, 'json');

            if (!is_array($data)) {
                throw new RuntimeException(sprintf('The %s response must contain a JSON object.', $provider->getName()));
            }

            $raw = [];

            foreach ($data as $key => $value) {
                if (!is_string($key)) {
                    throw new RuntimeException(sprintf('The %s response must contain a JSON object.', $provider->getName()));
                }

                $raw[$key] = $value;
            }

            $payload = $this->denormalizer->denormalize($raw, $type, 'json');
        } catch (ExceptionInterface $e) {
            throw $e;
        } catch (\Throwable $e) {
            throw new RuntimeException(sprintf('Decoding the %s response failed.', $provider->getName()), previous: $e);
        }

        if (!$payload instanceof $type) {
            throw new RuntimeException(sprintf('Decoding the %s response as "%s" failed.', $provider->getName(), $type));
        }

        return new Decoded($payload, $raw);
    }
}
