<?php

namespace OneToMany\AI\Bridge\Gemini\Normalizer;

use OneToMany\AI\Bridge\Gemini\Resource\Interaction\FileContent;
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
        $request = ['model' => $data->model->name];

        foreach ($data->getPrompt()->getInput() as $input) {
            if (!isset($request['input'])) {
                $request['input'] = [];
            }

            if ($input instanceof InputText) {
                $request['input'][] = new TextContent(
                    text: $input->getText(),
                );
            }

            if ($input instanceof RemoteFile) {
                $request['input'][] = FileContent::create(
                    $input->uri, mimeType: $input->mimeType,
                );
            }
        }

        if (null !== $instructions = $data->prompt->getInstructions()) {
            $request['system_instruction'] = $instructions->getText();
        }

        if ($data->getPrompt()->getSchema() instanceof JsonSchema) {
            $request['response_format'] = new TextResponseFormat(
                schema: $data->getPrompt()->getSchema()->getSchema(),
            );
        }

        return array_replace($data->getOptions(), $request);
    }

    /**
     * @see Symfony\Component\Serializer\Normalizer\NormalizerInterface
     */
    #[\Override]
    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof QueryDefinition && $data->getModel()->getVendor()->isGemini();
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
