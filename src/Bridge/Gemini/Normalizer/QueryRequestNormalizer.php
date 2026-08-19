<?php

namespace OneToMany\AI\Bridge\Gemini\Normalizer;

use OneToMany\AI\Bridge\QueryRequest;
use OneToMany\AI\Resource\File\RemoteFile;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

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
                return 'text';
            }

            $type = match (true) {
                $part->isAudio() => 'audio',
                $part->isImage() => 'image',
                $part->isVideo() => 'video',
                default => 'document',
            };

            return $type;
        };

        $input = [];

        foreach ($data->prompt->input as $part) {
            $type = $resolveType(part: $part);

            if (is_string($part)) {
                $input[] = [
                    'type' => $type,
                    'text' => $part,
                ];
            } else {
                $input[] = [
                    'type' => $type,
                    'uri' => $part->uri,
                    'mime_type' => $part->mediaType,
                ];
            }
        }

        $request = [
            'model' => $model,
            'stream' => false,
            'input' => $input,
        ];

        if (null !== $instructions = $data->prompt->instructions) {
            $request['system_instruction'] = trim($instructions);
        }

        if ($schema = $data->prompt->schema) {
            $request['response_format'] = [
                'type' => 'text',
                'mime_type' => $schema->mediaType,
                'schema' => $schema->schema,
            ];
        }

        return array_replace($data->options, $request);
    }

    /**
     * @see Symfony\Component\Serializer\Normalizer\NormalizerInterface
     */
    #[\Override]
    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof QueryRequest && $data->model->provider->isGemini();
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
