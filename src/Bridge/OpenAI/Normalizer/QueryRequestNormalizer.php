<?php

namespace OneToMany\AI\Bridge\OpenAI\Normalizer;

use OneToMany\AI\Bridge\QueryRequest;
use OneToMany\AI\Resource\File\RemoteFile;
use OneToMany\AI\Resource\Query\InputText;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

use function array_merge;
use function array_replace;

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
            InputText|RemoteFile $part,
        ): string {
            if ($part instanceof InputText) {
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

            if ($part instanceof InputText) {
                $content[] = [
                    'type' => $type,
                    'text' => (string) $part,
                ];
            }

            if ($part instanceof RemoteFile) {
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

        if (null !== $instructions = $data->prompt->instructions) {
            $request['instructions'] = $instructions->__toString();
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
