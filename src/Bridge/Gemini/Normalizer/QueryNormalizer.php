<?php

namespace OneToMany\AI\Bridge\Gemini\Normalizer;

use OneToMany\AI\Bridge\Gemini\Resource\Interaction\AudioContent;
use OneToMany\AI\Bridge\Gemini\Resource\Interaction\DocumentContent;
use OneToMany\AI\Bridge\Gemini\Resource\Interaction\FileContent;
use OneToMany\AI\Bridge\Gemini\Resource\Interaction\ImageContent;
use OneToMany\AI\Bridge\Gemini\Resource\Interaction\TextContent;
use OneToMany\AI\Bridge\Gemini\Resource\Interaction\TextResponseFormat;
use OneToMany\AI\Resource\File\RemoteFile;
use OneToMany\AI\Resource\Query\InputText;
use OneToMany\AI\Resource\Query\JsonSchema;
use OneToMany\AI\Resource\Query\QueryDefinition;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

use function array_replace;

final readonly class QueryNormalizer implements NormalizerInterface
{
    /**
     * @see Symfony\Component\Serializer\Normalizer\NormalizerInterface
     *
     * @param QueryDefinition $data
     *
     * @return array<string, mixed>
     */
    #[\Override]
    public function normalize(mixed $data, ?string $format = null, array $context = []): array
    {
        $request = ['model' => $data->model->name, 'input' => []];

        // $resolveType = static function (
        //     InputText|RemoteFile $part,
        // ): string {
        //     if ($part instanceof InputText) {
        //         return 'text';
        //     }

        //     $type = match (true) {
        //         $part->isAudio() => 'audio',
        //         $part->isImage() => 'image',
        //         $part->isVideo() => 'video',
        //         default => 'document',
        //     };

        //     return $type;
        // };

        foreach ($data->prompt->input() as $input) {
            if ($input instanceof InputText) {
                $request['input'][] = new TextContent(
                    text: $input->toString(),
                );
            }

            if ($input instanceof RemoteFile) {
                $request['input'][] = FileContent::create(
                    mimeType: $input->mimeType,
                    uri: $input->uri,
                );
            }
        }

        if (null !== $instructions = $data->prompt->instructions()) {
            $request['system_instruction'] = (string) $instructions;
        }

        if ($data->prompt->schema() instanceof JsonSchema) {
            $request['response_format'] = new TextResponseFormat(
                schema: $data->prompt->schema()->schema,
            );
        }

        //         'type' => 'text',
        //         'mime_type' => $schema->mediaType,
        //         'schema' => $schema->schema,
        //     ];
        // }

        return array_replace($data->options, $request);
    }

    /**
     * @see Symfony\Component\Serializer\Normalizer\NormalizerInterface
     */
    #[\Override]
    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof QueryDefinition && $data->model->provider->isGemini();
    }

    /**
     * @see Symfony\Component\Serializer\Normalizer\NormalizerInterface
     */
    #[\Override]
    public function getSupportedTypes(?string $format): array
    {
        return [
            QueryDefinition::class => false,
        ];
    }
}
