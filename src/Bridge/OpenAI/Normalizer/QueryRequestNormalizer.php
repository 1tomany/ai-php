<?php

namespace OneToMany\AI\Bridge\OpenAI\Normalizer;

use OneToMany\AI\Bridge\QueryRequest;
use OneToMany\AI\Resource\File\RemoteFile;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

use function array_merge;
use function array_replace;
use function is_string;
use function trim;

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
            string|RemoteFile $part,
        ): string {
            if (is_string($part)) {
                return 'input_text';
            }

            if ($part->isImage()) {
                return 'input_image';
            }

            return 'input_file';
        };

        $content = [];

        foreach ($data->prompt->input as $part) {
            $type = $resolveType(part: $part);

            if (is_string($part)) {
                $content[] = [
                    'type' => $type,
                    'text' => $part,
                ];
            } else {
                $content[] = [
                    'type' => $type,
                    'file_id' => $part->id,
                ];
            }
        }

        $request = [
            'model' => $model,
            'stream' => false,
            'input' => [
                [
                    'role' => 'user',
                    'content' => $content,
                ],
            ],
        ];

        if ($instructions = $data->prompt->instructions) {
            $request['instructions'] = trim($instructions);
        }

        if ($schema = $data->prompt->schema) {
            $request = array_merge($request, [
                'text' => [
                    'format' => [
                        'type' => 'json_schema',
                        'name' => $schema->name,
                        'strict' => $schema->strict,
                        'schema' => $schema->schema,
                    ],
                ],
            ]);
        }

        return array_replace($data->options, $request);
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
