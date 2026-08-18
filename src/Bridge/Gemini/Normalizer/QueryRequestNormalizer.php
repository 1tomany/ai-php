<?php

namespace OneToMany\AI\Bridge\Gemini\Normalizer;

use OneToMany\AI\Bridge\QueryRequest;
use OneToMany\AI\Exception\InvalidArgumentException;
use OneToMany\AI\Provider;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

use function array_replace;
use function is_string;
use function str_starts_with;

final readonly class QueryRequestNormalizer implements NormalizerInterface
{
    /**
     * @see Symfony\Component\Serializer\Normalizer\NormalizerInterface
     *
     * @param QueryRequest $data
     *
     * @return array<string, mixed>
     *
     * @throws InvalidArgumentException when a referenced file has no URI
     */
    #[\Override]
    public function normalize(mixed $data, ?string $format = null, array $context = []): array
    {
        $input = [];

        foreach ($data->prompt->input as $part) {
            if (is_string($part)) {
                $input[] = ['type' => 'text', 'text' => $part];

                continue;
            }

            if (null === $part->uri) {
                throw new InvalidArgumentException('A Gemini query requires a file URI.');
            }

            $input[] = [
                'type' => $this->contentType($part->mediaType),
                'uri' => $part->uri,
                'mime_type' => $part->mediaType,
            ];
        }

        $payload = [
            'model' => $data->model->name,
            'stream' => false,
            'input' => $input,
        ];

        if (null !== $data->prompt->instructions) {
            $payload['system_instruction'] = $data->prompt->instructions;
        }

        if (null !== $data->prompt->schema) {
            $payload['response_format'] = [
                'type' => 'text',
                'mime_type' => 'application/json',
                'schema' => $data->prompt->schema->schema,
            ];
        }

        return array_replace($data->options, $payload);
    }

    /**
     * @see Symfony\Component\Serializer\Normalizer\NormalizerInterface
     */
    #[\Override]
    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof QueryRequest
            && Provider::Gemini === $data->model->provider
            && (null === $format || 'json' === $format)
        ;
    }

    /**
     * @see Symfony\Component\Serializer\Normalizer\NormalizerInterface
     */
    #[\Override]
    public function getSupportedTypes(?string $format): array
    {
        return [
            QueryRequest::class => false,
        ];
    }

    private function contentType(string $mediaType): string
    {
        return match (true) {
            str_starts_with($mediaType, 'audio/') => 'audio',
            str_starts_with($mediaType, 'image/') => 'image',
            str_starts_with($mediaType, 'video/') => 'video',
            default => 'document',
        };
    }
}
