<?php

namespace OneToMany\AI\Bridge\OpenAI\Normalizer;

use OneToMany\AI\Bridge\InferenceRequest;
use OneToMany\AI\Exception\InvalidArgumentException;
use OneToMany\AI\Provider;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

use function array_replace;
use function is_string;
use function str_starts_with;

final readonly class InferenceRequestNormalizer implements NormalizerInterface
{
    /**
     * @see Symfony\Component\Serializer\Normalizer\NormalizerInterface
     *
     * @param InferenceRequest $data
     *
     * @return array<string, mixed>
     *
     * @throws InvalidArgumentException when the value cannot be normalized
     */
    #[\Override]
    public function normalize(mixed $data, ?string $format = null, array $context = []): array
    {
        if (!$this->supportsNormalization($data, $format, $context)) {
            throw new InvalidArgumentException('The OpenAI request normalizer received an unsupported value.');
        }

        $content = [];

        foreach ($data->prompt->input as $input) {
            if (is_string($input)) {
                $content[] = [
                    'type' => 'input_text',
                    'text' => $input,
                ];

                continue;
            }

            $content[] = [
                'type' => str_starts_with($input->mediaType, 'image/') ? 'input_image' : 'input_file',
                'file_id' => $input->id,
            ];
        }

        $payload = [
            'model' => $data->model->name,
            'stream' => false,
            'input' => [
                [
                    'role' => 'user',
                    'content' => $content,
                ],
            ],
        ];

        if (null !== $data->prompt->instructions) {
            $payload['instructions'] = $data->prompt->instructions;
        }

        if (null !== $data->prompt->schema) {
            $payload['text'] = [
                'format' => [
                    'type' => 'json_schema',
                    'name' => $data->prompt->schema->name,
                    'strict' => $data->prompt->schema->strict,
                    'schema' => $data->prompt->schema->schema,
                ],
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
        return $data instanceof InferenceRequest
            && Provider::OpenAI === $data->model->provider
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
            InferenceRequest::class => false,
        ];
    }
}
