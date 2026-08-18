<?php

namespace OneToMany\AI\Bridge\OpenAI\Normalizer;

use OneToMany\AI\Bridge\QueryRequest;
use OneToMany\AI\Resource\Files\RemoteFile;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

use function array_replace;
use function is_string;
use function stripos;

final readonly class QueryRequestNormalizer implements NormalizerInterface
{
    /**
     * @see Symfony\Component\Serializer\Normalizer\NormalizerInterface
     *
     * @param QueryRequest $data
     *
     * @return array<string, mixed>
     */
    #[\Override]
    public function normalize(mixed $data, ?string $format = null, array $context = []): array
    {
        $model = $data->model->name;

        $resolveType = static function (
            string|RemoteFile $i,
        ): string {
            if (is_string($i)) {
                return 'input_text';
            }

            return 0 === stripos($i->mediaType, 'image/') ? 'input_image' : 'input_file';
        };

        $content = [];

        foreach ($data->prompt->input as $input) {
            $type = $resolveType($input);

            if (is_string($input)) {
                $content[] = [
                    'type' => $type,
                    'text' => $input,
                ];
            } else {
                $content[] = [
                    'type' => $type,
                    'file_id' => $input->id,
                ];
            }
        }

        $payload = [
            'model' => $model,
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
            $schema = $data->prompt->schema;

            $payload['text'] = [
                'format' => [
                    'type' => 'json_schema',
                    'name' => $schema->name,
                    'strict' => $schema->strict,
                    'schema' => $schema->schema,
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
        return $data instanceof QueryRequest && $data->model->provider->isOpenAI();
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
}
