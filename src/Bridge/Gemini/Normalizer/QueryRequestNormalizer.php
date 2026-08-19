<?php

namespace OneToMany\AI\Bridge\Gemini\Normalizer;

use OneToMany\AI\Bridge\QueryRequest;
use OneToMany\AI\Resource\File\RemoteFile;
use OneToMany\AI\Resource\Query\InputText;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

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

        foreach ($data->prompt->getInput() as $part) {
            $type = $resolveType(part: $part);

            if ($part instanceof InputText) {
                $input[] = [
                    'type' => $type,
                    'text' => (string) $part,
                ];
            }

            if ($part instanceof RemoteFile) {
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

        if (null !== $instructions = $data->prompt->getInstructions()) {
            $request['system_instruction'] = (string) $instructions;
        }

        if ($schema = $data->prompt->getSchema()) {
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
