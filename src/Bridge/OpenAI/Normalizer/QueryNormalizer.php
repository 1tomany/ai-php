<?php

namespace OneToMany\AI\Bridge\OpenAI\Normalizer;

use OneToMany\AI\Bridge\OpenAI\Resource\Response\EasyInputMessage;
use OneToMany\AI\Bridge\OpenAI\Resource\Response\ResponseInput;
use OneToMany\AI\Bridge\OpenAI\Resource\Response\ResponseInputFile;
use OneToMany\AI\Bridge\OpenAI\Resource\Response\ResponseInputImage;
use OneToMany\AI\Bridge\OpenAI\Resource\Response\ResponseInputText;
use OneToMany\AI\Resource\File\RemoteFile;
use OneToMany\AI\Resource\Query\InputText;
use OneToMany\AI\Resource\Query\QueryDefinition;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

use function array_merge;
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
        $content = [];

        foreach ($data->prompt->input() as $input) {
            if ($input instanceof InputText) {
                $content[] = new ResponseInputText(
                    text: $input->text,
                );
            }

            if ($input instanceof RemoteFile) {
                $content[] = ResponseInput::asFile(
                    $input->mimeType, $input->id,
                );
            }
        }

        $input = new EasyInputMessage(...[
            'content' => $content,
        ]);

        $request = [
            'model' => $data->model->name,
            'input' => [
                $input,
            ],
            'stream' => false,
        ];

        if (null !== $instructions = $data->prompt->instructions()) {
            $request['instructions'] = $instructions->toString();
        }

        if ($schema = $data->prompt->schema()) {
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
        return $data instanceof QueryDefinition && $data->model->provider->isOpenAI();
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
